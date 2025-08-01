<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/VolunteerMatching.php');
    exit;
}

$volunteerId = intval($_POST['volunteer_name'] ?? 0);
$eventId     = intval($_POST['matched_event']  ?? 0);

$errors = [];
if ($volunteerId <= 0) { $errors[] = 'Volunteer is required.'; }
if ($eventId     <= 0) { $errors[] = 'Event selection is required.'; }

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: /pages/VolunteerMatching.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO VolunteerHistory
          (user_id, event_id, status)
        VALUES
          (:uid, :eid, 'registered')
    ");
    $stmt->execute([
        ':uid' => $volunteerId,
        ':eid' => $eventId,
    ]);

    header('Location: /pages/VolunteerMatching.php?success=1');
    exit;

} catch (PDOException $e) {
    error_log('process_match error: ' . $e->getMessage());
    $_SESSION['errors'] = ['Failed to match volunteer. Please try again.'];
    header('Location: /pages/VolunteerMatching.php');
    exit;
}
