<?php
require_once __DIR__ . '/../db.php';

$stmt = $pdo->prepare("SELECT user_id, email AS volunteer_name FROM UserCredentials WHERE role = 'volunteer' ORDER BY email");
$stmt->execute();
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT event_id, event_name FROM EventDetails ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['comment_success'] ?? false;
unset($_SESSION['errors'], $_SESSION['comment_success']);
?>

<div class="event-container">
  <h2>Comment on Volunteer</h2>

  <?php if ($success): ?>
    <div class="success-message" style="background: #ddffdd; border: 1px solid #2e7d32; padding: 10px; border-radius: 5px; color: #2e7d32; margin-bottom: 15px;">
      Comment added successfully!
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="error-messages">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="/backend/controllers/process_comment.php" method="POST">
    <div class="form-group">
      <label for="event_id">Event <span>*</span></label>
      <select name="event_id" id="event_id" required>
        <option value="" disabled selected>Select Event</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>"><?= htmlspecialchars($e['event_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="volunteer_id">Volunteer <span>*</span></label>
      <select name="volunteer_id" id="volunteer_id" required>
        <option value="" disabled selected>Select Volunteer</option>
        <?php foreach ($volunteers as $v): ?>
          <option value="<?= $v['user_id'] ?>"><?= htmlspecialchars($v['volunteer_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
        <label for="assignment_id">Assignment <span>*</span></label>
        <select name="assignment_id" id="assignment_id" required>
            <option value="" disabled selected>Select Assignment</option>
        </select>
    </div>

    <div class="form-group">
      <label for="comment">Comment <span>*</span></label>
      <textarea name="comment" id="comment" rows="4" required></textarea>
    </div>

    <button type="submit">Submit Comment</button>
  </form>
</div>

<script>
const eventSelect = document.getElementById('event_id');
const volunteerSelect = document.getElementById('volunteer_id');
const assignmentSelect = document.getElementById('assignment_id');

function fetchAssignments(eventId, volunteerId) {
  if (!eventId || !volunteerId) return;

  fetch(`/backend/ajax/get_assignments.php?event_id=${eventId}&volunteer_id=${volunteerId}`)
    .then(res => res.json())
    .then(data => {
      assignmentSelect.innerHTML = '<option value="" disabled selected>Select Assignment</option>';
      data.forEach(a => {
        if (a.name?.trim()) {
          assignmentSelect.innerHTML += `<option value="${a.assignment_id}">${a.name}</option>`;
        }
      });
    });
}

eventSelect.addEventListener('change', () => {
  fetchAssignments(eventSelect.value, volunteerSelect.value);
});

volunteerSelect.addEventListener('change', () => {
  fetchAssignments(eventSelect.value, volunteerSelect.value);
});
</script>