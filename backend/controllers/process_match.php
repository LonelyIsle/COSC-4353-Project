// File: backend/auth/process_match.php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/VolunteerMatching.php');
    exit;
}

$volunteerName = trim($_POST['volunteer_name'] ?? '');
$matchedEvent  = trim($_POST['matched_event'] ?? '');

$errors = [];
if ($volunteerName === '') { $errors[] = 'Volunteer is required.'; }
if ($matchedEvent  === '') { $errors[] = 'Event selection is required.'; }

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: /pages/VolunteerMatching.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO volunteer_matches 
         (user_id, volunteer_name, event_name)
         VALUES (:uid, :vol, :evt)"
    );
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':vol' => $volunteerName,
        ':evt' => $matchedEvent,
    ]);

    header('Location: /pages/VolunteerMatching.php?success=1');
} catch (PDOException $e) {
    $_SESSION['errors'] = ['Failed to match volunteer. Please try again.'];
    header('Location: /pages/VolunteerMatching.php');
}
exit;
