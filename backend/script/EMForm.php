<?php
require_once __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

$skillsOptions = [
    'Communication','Teamwork','Leadership',
    'Programming','Design','Marketing','Management',
];

$urgencyLevels = ['low','medium','high','critical'];

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="event-container">
  <h2>Create New Event</h2>

  <?php if (!empty($errors)): ?>
    <div class="error-messages" style="background: #ffdddd; border: 1px solid #c00; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><ul>
      <?php foreach ($errors as $e): ?>
        <li><?=htmlspecialchars($e)?></li>
      <?php endforeach; ?>
    </ul></div>
  <?php endif; ?>

  <?php if (isset($_GET['success'])): ?>
    <div class="success-message" style="background: #ddffdd; border-color: #2e7d32; color: #2e7d32;">
      Event created successfully!
    </div>
  <?php endif; ?>

  <form action="/backend/controllers/submit_event.php" method="post">
    <div class="form-group">
      <label for="event_name">Event Name <span>*</span></label><br>
      <input type="text" id="event_name" name="event_name" maxlength="100" required placeholder="Enter event name">
    </div>
    <div class="form-group">
      <label for="event_description">Event Description <span>*</span></label><br>
      <textarea id="event_description" name="event_description" rows="4" required placeholder="Describe the event"></textarea>
    </div>
    <div class="form-group">
      <label for="location">Location <span>*</span></label><br>
      <textarea id="location" name="location" rows="2" required placeholder="Enter the venue or address"></textarea>
    </div>
    <div class="form-group">
      <label for="required_skills">Required Skills <span>*</span></label><br>
      <select id="required_skills" name="required_skills[]" multiple size="<?=min(5, count($skillsOptions))?>" required>
        <?php foreach ($skillsOptions as $skill): ?>
          <option value="<?=htmlspecialchars($skill)?>"><?=htmlspecialchars($skill)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="urgency">Urgency <span>*</span></label><br>
      <select id="urgency" name="urgency" required>
        <option value="" disabled selected>Select urgency</option>
        <?php foreach ($urgencyLevels as $level): ?>
          <option value="<?=htmlspecialchars($level)?>"><?=ucfirst($level)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="event_date">Event Date <span>*</span></label><br>
      <input type="date" id="event_date" name="event_date" required>
    </div>

    <button type="submit">Create Event</button>
  </form>
</div>
