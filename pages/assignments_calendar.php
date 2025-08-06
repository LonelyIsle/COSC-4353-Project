<?php
// pages/assignments_calendar.php
require_once __DIR__ . '/../backend/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo "<p>Please <a href=\"/pages/Login.php\">login</a> first.</p>";
    return;
}

// fetch this volunteer's assigned events
$stmt = $pdo->prepare("
    SELECT ed.event_date, ed.event_name
      FROM EventDetails ed
      JOIN VolunteerHistory vh ON ed.event_id = vh.event_id
     WHERE vh.user_id = :uid
     ORDER BY ed.event_date ASC
");
$stmt->execute([':uid' => $userId]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="assignments-container">
  <h3>My Assigned Events</h3>

  <?php if (empty($assignments)): ?>
    <p>You have no assigned events.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($assignments as $a): ?>
        <li>
          <strong><?= htmlspecialchars($a['event_date']) ?></strong>
          &nbsp;–&nbsp;
          <?= htmlspecialchars($a['event_name']) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
