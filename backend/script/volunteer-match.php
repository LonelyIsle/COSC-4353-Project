<?php
require_once __DIR__ . '/../db.php';

$stmt = $pdo->prepare("SELECT user_id, email AS volunteer_name FROM UserCredentials WHERE role = 'volunteer' ORDER BY email");
$stmt->execute();
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT event_id, event_name FROM EventDetails ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = $_SESSION['errors'] ?? [];
$success = isset($_GET['success']);
unset($_SESSION['errors']);
?>

<div class="event-container">
  <h2>Volunteer Matching</h2>

  <?php if ($success): ?>
    <div class="success-message" style="background: #ddffdd; border-color: #2e7d32; color: #2e7d32;">Volunteer matched successfully!</div>
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

  <form action="/backend/controllers/process_match.php" method="post">
    <div class="form-group">
      <label for="volunteer_name">Volunteer Name <span>*</span></label>
      <select id="volunteer_name" name="volunteer_name" required>
        <option value="" disabled selected>Select a volunteer</option>
        <?php foreach ($volunteers as $v): ?>
          <option value="<?= $v['user_id'] ?>"><?= htmlspecialchars($v['volunteer_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="matched_event">Matched Event <span>*</span></label>
      <select id="matched_event" name="matched_event" required>
        <option value="" disabled selected>Select matched event</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>"><?= htmlspecialchars($e['event_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit">Match Volunteer</button>
  </form>
</div>
