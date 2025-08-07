<?php
session_start();
require_once __DIR__ . '/../db.php';

$eventId = intval($_POST['event_id'] ?? 0);
$volunteerId = intval($_POST['volunteer_id'] ?? 0);
$assignmentId = intval($_POST['assignment_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$adminId = $_SESSION['user_id'] ?? 0;

$errors = [];

if ($eventId <= 0) $errors[] = "Invalid event.";
if ($volunteerId <= 0) $errors[] = "Invalid volunteer.";
if ($assignmentId <= 0) $errors[] = "Invalid assignment.";
if (empty($comment)) $errors[] = "Comment is required.";

if ($errors) {
    $_SESSION['errors'] = $errors;
    header("Location: /pages/admin_dashboard.php?tab=comment_volunteer&event_id=$eventId&volunteer_id=$volunteerId");
    exit;
}

// Insert the comment into VolunteerHistory
$stmt = $pdo->prepare("
    UPDATE VolunteerHistory
    SET comments = :comment
    WHERE user_id = :user_id AND event_id = :event_id
");
$stmt->execute([
    ':comment' => $comment,
    ':user_id' => $volunteerId,
    ':event_id' => $eventId
]);

$_SESSION['success'] = true;
header("Location: /pages/admin_dashboard.php?tab=comment_volunteer&event_id=$eventId");
exit;
?>