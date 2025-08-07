<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';

/**
 * Parse skills that may be JSON (["A","B"]) or CSV ("A, B"),
 * sometimes with brackets or single quotes. Return lowercase array.
 */
function parseSkillsToArray(?string $raw): array {
    if ($raw === null) return [];
    $raw = trim($raw);
    if ($raw === '') return [];

    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $skills = $decoded;
    } else {
        if ($raw[0] === '[' && substr($raw, -1) === ']') {
            $inner = trim(substr($raw, 1, -1));
            $coerced = '[' . str_replace("'", '"', $inner) . ']';
            $decoded2 = json_decode($coerced, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                $skills = $decoded2;
            } else {
                $skills = array_map('trim', explode(',', $inner));
            }
        } else {
            $skills = array_map('trim', explode(',', $raw));
        }
    }

    $out = [];
    foreach ($skills as $s) {
        if (!is_string($s)) continue;
        $s = trim($s);
        if ($s !== '') $out[] = mb_strtolower($s);
    }
    return array_values(array_unique($out));
}

// Volunteers + their skills
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

// Events + required skills
$stmt = $pdo->prepare("
    SELECT event_id,
           event_name,
           COALESCE(required_skills, '') AS required_skills
      FROM EventDetails
  ORDER BY event_date ASC, event_name ASC
");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Precompute normalized arrays for data-* attributes
foreach ($volunteers as &$v) {
    $v['_skills_array'] = parseSkillsToArray($v['volunteer_skills'] ?? '');
}
unset($v);
foreach ($events as &$e) {
    $e['_req_array'] = parseSkillsToArray($e['required_skills'] ?? '');
}
unset($e);

// Scoped errors/success for this tab
$errors  = $_SESSION['vm_errors'] ?? [];
unset($_SESSION['vm_errors']);
$success = isset($_GET['successvm']);
?>
<div class="event-container">
  <h2>Volunteer Matching</h2>

  <?php if ($success): ?>
    <div class="success-message" style="background:#ddffdd;border:1px solid #2e7d32;color:#2e7d32;padding:10px;border-radius:5px;margin-bottom:15px;">
      Volunteer matched successfully!
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="error-messages" style="background:#ffdddd;border:1px solid #c00;padding:10px;border-radius:5px;margin-bottom:15px;">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="/backend/controllers/process_match.php" method="post">
    <!-- Pick EVENT first -->
    <div class="form-group" style="margin-bottom:12px;">
      <label for="matched_event">Matched Event <span>*</span></label><br>
      <select id="matched_event" name="matched_event" required>
        <option value="" disabled selected>Select an event</option>
        <?php foreach ($events as $e): ?>
          <option
            value="<?= (int)$e['event_id'] ?>"
            data-reqskills='<?= htmlspecialchars(json_encode($e["_req_array"]), ENT_QUOTES, "UTF-8") ?>'
          >
            <?= htmlspecialchars($e['event_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div id="event-req-note" style="font-size:12px;color:#555;margin-top:4px;">
        Pick an event to filter eligible volunteers.
      </div>
    </div>

    <!-- Then pick VOLUNTEER (filtered by event skills) -->
    <div class="form-group" style="margin-bottom:12px;">
      <label for="volunteer_name">Volunteer <span>*</span></label><br>
      <select id="volunteer_name" name="volunteer_name" required disabled>
        <option value="" disabled selected>Select an event first</option>
        <?php foreach ($volunteers as $v): ?>
          <option
            value="<?= (int)$v['user_id'] ?>"
            data-skills='<?= htmlspecialchars(json_encode($v["_skills_array"]), ENT_QUOTES, "UTF-8") ?>'
          >
            <?= htmlspecialchars($v['volunteer_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div id="vol-help" style="font-size:12px;color:#555;margin-top:4px;"></div>
    </div>

    <button type="submit">Match Volunteer</button>
  </form>
</div>

<script>
(function(){
  const eventSelect  = document.getElementById('matched_event');
  const volSelect    = document.getElementById('volunteer_name');
  const eventReqNote = document.getElementById('event-req-note');
  const volHelp      = document.getElementById('vol-help');

  const allVolunteerOptions = Array.from(volSelect.querySelectorAll('option')).filter(o => o.value !== '');

  function hasOverlap(a, b) {
    if (!a || !b) return false;
    const setB = new Set(b);
    for (const x of a) if (setB.has(x)) return true;
    return false;
  }

  eventSelect.addEventListener('change', () => {
    const selected = eventSelect.options[eventSelect.selectedIndex];
    let reqSkills = [];
    try { reqSkills = JSON.parse(selected.getAttribute('data-reqskills') || '[]'); } catch(e) {}

    eventReqNote.textContent = reqSkills.length
      ? `Required skills for this event: ${reqSkills.join(', ')}`
      : 'This event has no specific required skills. Showing all volunteers.';

    // Build eligible list
    const eligible = [];
    for (const opt of allVolunteerOptions) {
      let vskills = [];
      try { vskills = JSON.parse(opt.getAttribute('data-skills') || '[]'); } catch(e) {}
      if (reqSkills.length === 0 || hasOverlap(vskills, reqSkills)) {
        eligible.push({opt, overlap: vskills.filter(s => reqSkills.includes(s)).length});
      }
    }

    volSelect.innerHTML = '';
    if (!eligible.length) {
      const warn = document.createElement('option');
      warn.textContent = 'No eligible volunteers found';
      warn.disabled = true;
      volSelect.appendChild(warn);
      volHelp.textContent = 'No volunteers match the event’s required skills.';
      volSelect.disabled = true;
      return;
    }

    // Sort: greatest overlap first, then by name
    eligible.sort((a, b) => b.overlap - a.overlap || a.opt.text.localeCompare(b.opt.text));
    for (const e of eligible) volSelect.appendChild(e.opt.cloneNode(true));
    volSelect.disabled = false;
    volSelect.selectedIndex = 0;
    volHelp.textContent = 'Only volunteers with the required skills are shown (best matches on top).';
  });
})();
</script>
