<?php
require_once __DIR__ . '/../db.php';

$event_id = $_GET['event_id'] ?? null;
$volunteer_id = $_GET['volunteer_id'] ?? null;
if (!$event_id || !$volunteer_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
  SELECT assignment_id, name
  FROM EventAssignments
  WHERE event_id = :eventId AND user_id = :volunteerId
  ORDER BY assigned_at DESC
");
$stmt->execute([
  'eventId' => $_GET['event_id'],
  'volunteerId' => $_GET['volunteer_id']
]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($assignments);
?>