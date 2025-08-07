<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';

/**
 * Normalize a "skills" field that may be JSON (["Leadership","First Aid"])
 * or CSV ("Leadership, First Aid").
 * Returns a lowercased, trimmed array of unique skills.
 */
function parseSkillsToArray(?string $raw): array {
    if ($raw === null || $raw === '') return [];
    $skills = [];

    // Try JSON first
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $skills = $decoded;
    } else {
        // Fallback: CSV
        $skills = array_map('trim', explode(',', $raw));
    }

    // Normalize
    $norm = [];
    foreach ($skills as $s) {
        if (!is_string($s)) continue;
        $s = trim($s);
        if ($s === '') continue;
        $norm[] = mb_strtolower($s);
    }
    return array_values(array_unique($norm));
}

// Fetch volunteers + their skills from UserProfile
$stmt = $pdo->prepare("
    SELECT uc.user_id,
           uc.email AS volunteer_name,
           up.skills AS volunteer_skills
      FROM UserCredentials uc
 LEFT JOIN UserProfile up ON up.user_id = uc.user_id
     WHERE uc.role = 'volunteer'
  ORDER BY uc.email
");
$stmt->execute();
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch events + required skills
$stmt = $pdo->prepare("
    SELECT event_id,
           event_name,
           COALESCE(required_skills, '') AS required_skills
      FROM EventDetails
  ORDER BY event_date ASC, event_name ASC
");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Precompute normalized skill arrays for data-* attributes
foreach ($volunteers as &$v) {
    $v['_skills_array'] = parseSkillsToArray($v['volunteer_skills'] ?? '');
}
unset($v);
foreach ($events as &$e) {
    $e['_req_array'] = parseSkillsToArray($e['required_skills'] ?? '');
}
unset($e);

$errors  = $_SESSION['errors']  ?? [];
unset($_SESSION['errors']);
$success = isset($_GET['successvm']);
?>
<div class="event-container">
  <h2>Volunteer Matching</h2>

  <?php if ($success): ?>
    <div class="success-message" style="background: #ddffdd; border: 1px solid #2e7d32; color: #2e7d32; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
      Volunteer matched successfully!
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="error-messages" style="background: #ffdddd; border: 1px solid #c00; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="/backend/controllers/process_match.php" method="post">
    <div class="form-group" style="margin-bottom: 12px;">
      <label for="volunteer_name">Volunteer <span>*</span></label><br>
      <select id="volunteer_name" name="volunteer_name" required>
        <option value="" disabled selected>Select a volunteer</option>
        <?php foreach ($volunteers as $v): ?>
          <option
            value="<?= (int)$v['user_id'] ?>"
            data-skills='<?= htmlspecialchars(json_encode($v["_skills_array"]), ENT_QUOTES, "UTF-8") ?>'
          >
            <?= htmlspecialchars($v['volunteer_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div id="vol-skills-note" style="font-size: 12px; color: #555; margin-top: 4px;"></div>
    </div>

    <div class="form-group" style="margin-bottom: 12px;">
      <label for="matched_event">Matched Event <span>*</span></label><br>
      <select id="matched_event" name="matched_event" required disabled>
        <option value="" disabled selected>Select a volunteer first</option>
        <?php foreach ($events as $e): ?>
          <option
            value="<?= (int)$e['event_id'] ?>"
            data-reqskills='<?= htmlspecialchars(json_encode($e["_req_array"]), ENT_QUOTES, "UTF-8") ?>'
          >
            <?= htmlspecialchars($e['event_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div id="event-help" style="font-size: 12px; color: #555; margin-top: 4px;">
        Events will be filtered by skill match once you select a volunteer.
      </div>
    </div>

    <button type="submit">Match Volunteer</button>
  </form>
</div>

<script>
(function(){
  const volSelect   = document.getElementById('volunteer_name');
  const eventSelect = document.getElementById('matched_event');
  const volNote     = document.getElementById('vol-skills-note');
  const eventHelp   = document.getElementById('event-help');

  // Keep a copy of all event <option> nodes so we can re-filter without reloading
  const allEventOptions = Array.from(eventSelect.querySelectorAll('option')).filter(o => o.value !== '');

  function overlap(a, b) {
    if (!a || !b) return false;
    const setB = new Set(b);
    for (const x of a) { if (setB.has(x)) return true; }
    return false;
  }

  volSelect.addEventListener('change', () => {
    const selected = volSelect.options[volSelect.selectedIndex];
    let volSkills = [];
    try { volSkills = JSON.parse(selected.getAttribute('data-skills') || '[]'); } catch(e) { volSkills = []; }

    volNote.textContent = volSkills.length
      ? `Volunteer skills: ${volSkills.join(', ')}`
      : 'No skills on file for this volunteer. Showing all events.';

    // Filter/sort event options
    const matching = [];
    const nonMatching = [];
    for (const opt of allEventOptions) {
      let req = [];
      try { req = JSON.parse(opt.getAttribute('data-reqskills') || '[]'); } catch(e) { req = []; }
      if (req.length === 0 || overlap(volSkills, req)) {
        // Either event has no requirements, or skills overlap → allow
        matching.push({opt, reqCount: req.length});
      } else {
        nonMatching.push(opt);
      }
    }

    // Sort matching by "more requirements first" (rough proxy for specificity),
    // then by label text (stable-ish)
    matching.sort((a, b) => b.reqCount - a.reqCount || a.opt.text.localeCompare(b.opt.text));

    // Rebuild event list
    eventSelect.innerHTML = '';
    if (matching.length === 0) {
      const warn = document.createElement('option');
      warn.textContent = 'No skill-matching events; showing non-matching:';
      warn.disabled = true;
      eventSelect.appendChild(warn);
      nonMatching.forEach(opt => eventSelect.appendChild(opt.cloneNode(true)));
      eventHelp.textContent = 'No overlapping skills found. You may still choose a non-matching event.';
      eventSelect.disabled = false;
    } else {
      matching.forEach(entry => eventSelect.appendChild(entry.opt.cloneNode(true)));
      // Optionally append a separator + non-matching at the end
      if (nonMatching.length) {
        const sep = document.createElement('option');
        sep.textContent = '────────── Non-matching below ──────────';
        sep.disabled = true;
        eventSelect.appendChild(sep);
        nonMatching.forEach(opt => eventSelect.appendChild(opt.cloneNode(true)));
      }
      eventHelp.textContent = 'Top of the list = best skill fit.';
      eventSelect.disabled = false;
      // Preselect the top suggestion
      eventSelect.selectedIndex = 0;
    }
  });
})();
</script>
