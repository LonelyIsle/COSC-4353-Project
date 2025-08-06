<?php
// backend/controllers/fetch_assignments.php
session_start();
header('Content-Type: application/json');

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    http_response_code(403);
    echo json_encode(['error'=>'Access denied']);
    exit;
}

require_once __DIR__ . '/../db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(400);
    echo json_encode(['error'=>'Not logged in']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT ed.event_id, ed.event_name, ed.event_date
      FROM EventDetails ed
      JOIN VolunteerHistory vh ON ed.event_id = vh.event_id
     WHERE vh.user_id = :uid
     ORDER BY ed.event_date ASC
");
$stmt->execute([':uid'=>$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
