<?php
require_once __DIR__ . '/../db.php';

$selected_event = $_GET['event_id'] ?? '';
$selected_volunteer = $_GET['volunteer_id'] ?? '';

$events = $pdo->query("SELECT event_id, event_name FROM EventDetails ORDER BY event_date ASC")->fetchAll();

$volunteers = [];
if ($selected_event) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT uc.user_id, uc.email
        FROM EventAssignments ea
        JOIN UserCredentials uc ON ea.user_id = uc.user_id
        WHERE ea.event_id = ?
    ");
    $stmt->execute([$selected_event]);
    $volunteers = $stmt->fetchAll();
}

$assignments = [];
if ($selected_event && $selected_volunteer) {
    $stmt = $pdo->prepare("
        SELECT assignment_id, name
        FROM EventAssignments
        WHERE event_id = ? AND user_id = ?
    ");
    $stmt->execute([$selected_event, $selected_volunteer]);
    $assignments = $stmt->fetchAll();
}
?>

<div class="event-container">
  <h2>Comment on Volunteer</h2>

  <form method="GET" action="">
    <input type="hidden" name="tab" value="comment_volunteer">

    <div class="form-group">
      <label for="event_id">Select Event</label>
      <select name="event_id" id="event_id" onchange="this.form.submit()" required>
        <option value="">-- Choose an event --</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>" <?= $e['event_id'] == $selected_event ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['event_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if ($selected_event): ?>
      <div class="form-group">
        <label for="volunteer_id">Select Volunteer</label>
        <select name="volunteer_id" id="volunteer_id" onchange="this.form.submit()" required>
          <option value="">-- Choose a volunteer --</option>
          <?php foreach ($volunteers as $v): ?>
            <option value="<?= $v['user_id'] ?>" <?= $v['user_id'] == $selected_volunteer ? 'selected' : '' ?>>
              <?= htmlspecialchars($v['email']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>
  </form>

  <?php if ($selected_event && $selected_volunteer): ?>
    <form method="POST" action="/backend/controllers/process_comment.php">
      <input type="hidden" name="event_id" value="<?= htmlspecialchars($selected_event) ?>">
      <input type="hidden" name="volunteer_id" value="<?= htmlspecialchars($selected_volunteer) ?>">

      <div class="form-group">
        <label for="assignment_id">Select Assignment</label>
        <select name="assignment_id" id="assignment_id" required>
          <option value="">-- Choose an assignment --</option>
          <?php foreach ($assignments as $a): ?>
            <option value="<?= $a['assignment_id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="comment">Comment</label>
        <textarea name="comment" id="comment" rows="4" required></textarea>
      </div>

      <button type="submit">Submit Comment</button>
    </form>
  <?php endif; ?>
</div>
