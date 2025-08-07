<?php
session_start();
require_once __DIR__ . '/../db.php';

$eventId = intval($_POST['event_id'] ?? 0);
$userId = intval($_POST['user_id'] ?? 0);
$assignmentId = intval($_POST['assignment_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

$errors = [];

if ($eventId <= 0) $errors[] = "Invalid event selected.";
if ($userId <= 0) $errors[] = "Invalid volunteer selected.";
if ($assignmentId <= 0) $errors[] = "Invalid assignment selected.";
if (empty($comment)) $errors[] = "Comment cannot be empty.";

if ($errors) {
    $_SESSION['errors'] = $errors;
    header("Location: /pages/admin_dashboard.php?tab=comment-volunteer");
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM EventAssignments
    WHERE assignment_id = :assignment_id AND user_id = :user_id AND event_id = :event_id
");
$stmt->execute([
    ':assignment_id' => $assignmentId,
    ':user_id' => $userId,
    ':event_id' => $eventId
]);

if (!$stmt->fetch()) {
    $_SESSION['errors'] = ["Selected assignment does not match selected event and volunteer."];
    header("Location: /pages/admin_dashboard.php?tab=comment-volunteer");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO VolunteerHistory (user_id, event_id, status, comments)
    VALUES (:user_id, :event_id, 'commented', :comment)
");

$stmt->execute([
    ':user_id' => $userId,
    ':event_id' => $eventId,
    ':comment' => $comment
]);

$_SESSION['comment_success'] = true;
header("Location: /pages/admin_dashboard.php?tab=comment-volunteer");
exit;
?>
