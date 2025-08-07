<?php
require_once __DIR__ . '/../db.php';

$volStmt = $pdo->prepare("SELECT user_id, email FROM UserCredentials WHERE role = 'volunteer' ORDER BY email");
$volStmt->execute();
$volunteers = $volStmt->fetchAll(PDO::FETCH_ASSOC);

$eventStmt = $pdo->prepare("SELECT event_id, event_name FROM EventDetails ORDER BY event_date");
$eventStmt->execute();
$events = $eventStmt->fetchAll(PDO::FETCH_ASSOC);

$errors = $_SESSION['errors'] ?? [];
$success = isset($_GET['success']);
unset($_SESSION['errors']);
?>

<div class="event-container">
  <h2>Create Event Assignment</h2>

  <?php if ($success): ?>
    <div class="success-message" style="background: #ddffdd; border: 1px solid #2e7d32; padding: 10px; border-radius: 5px; color: #2e7d32; margin-bottom: 15px;">
      Volunteer successfully assigned!
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

  <form method="POST" action="/backend/controllers/process_assignment.php">
    <div class="form-group">
      <label for="user_id">Volunteer</label>
      <select name="user_id" id="user_id" required>
        <option value="">Select volunteer</option>
        <?php foreach ($volunteers as $v): ?>
          <option value="<?= $v['user_id'] ?>"><?= htmlspecialchars($v['email']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="event_id">Event</label>
      <select name="event_id" id="event_id" required>
        <option value="">Select event</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>"><?= htmlspecialchars($e['event_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
        <label for="name">Assignment Name</label>
        <input type="text" name="name" id="name" placeholder="e.g. Registration Booth, Tech Support" required>
    </div>

    <div class="form-group">
      <label for="description">Assignment Description</label>
      <textarea name="description" id="description" rows="4" placeholder="Describe the volunteer's role or expectations..." required></textarea>
    </div>

    <button type="submit">Assign Volunteer</button>
  </form>
</div>