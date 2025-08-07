<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../db.php';

$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
  SELECT DISTINCT uc.user_id, uc.email
  FROM EventAssignments ea
  JOIN UserCredentials uc ON ea.user_id = uc.user_id
  WHERE ea.event_id = ?
");
$stmt->execute([$event_id]);
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($volunteers);
?>