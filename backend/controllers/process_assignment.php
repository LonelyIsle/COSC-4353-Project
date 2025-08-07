<?php
session_start();
require_once __DIR__ . '/../db.php';

$userId = intval($_POST['user_id'] ?? 0);
$eventId = intval($_POST['event_id'] ?? 0);
$assignedBy = $_SESSION['user_id'] ?? 0;
$description = trim($_POST['description'] ?? '');
$name = trim($_POST['name'] ?? '');

$errors = [];
if ($userId <= 0) $errors[] = "Invalid volunteer selected.";
if ($eventId <= 0) $errors[] = "Invalid event selected.";
if (empty($description)) $errors[] = "Description is required.";
if (empty($name)) $errors[] = "Assignment name is required.";

if ($errors) {
    $_SESSION['errors'] = $errors;
    header("Location: /pages/admin_dashboard.php?tab=create-assignment");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO EventAssignments (user_id, event_id, assigned_by, assigned_at, description, name)
    VALUES (:user_id, :event_id, :assigned_by, NOW(), :description, :name)
");

$stmt->execute([
    ':user_id'     => $userId,
    ':event_id'    => $eventId,
    ':assigned_by' => $assignedBy,
    ':description' => $description,
    ':name'        => $name
]);

$notificationMessage = "You have been assigned to \"$name\" for an event. Description: $description";
$notifyStmt = $pdo->prepare("
    INSERT INTO Notifications (user_id, message, is_read, sent_at)
    VALUES (:user_id, :message, 0, NOW())
");
$notifyStmt->execute([
    ':user_id' => $userId,
    ':message' => $notificationMessage
]);

header("Location: /pages/admin_dashboard.php?tab=create-assignment&success=1");
exit;
?>