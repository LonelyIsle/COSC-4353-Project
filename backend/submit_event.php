// File: backend/auth/submit_event.php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

require_once __DIR__ . '/../db.php';  

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/EMForm.php');
    exit;
}

$eventName        = trim($_POST['event_name'] ?? '');
$eventDescription = trim($_POST['event_description'] ?? '');
$location         = trim($_POST['location'] ?? '');
$requiredSkills   = $_POST['required_skills'] ?? [];
$urgency          = trim($_POST['urgency'] ?? '');
$eventDate        = trim($_POST['event_date'] ?? '');

$errors = [];
if ($eventName === '')        { $errors[] = 'Event Name is required.'; }
if ($eventDescription === '') { $errors[] = 'Description is required.'; }
if ($location === '')         { $errors[] = 'Location is required.'; }
if (empty($requiredSkills))   { $errors[] = 'Select at least one skill.'; }
if ($urgency === '')          { $errors[] = 'Urgency level is required.'; }
if ($eventDate === '')        { $errors[] = 'Event date is required.'; }

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: /pages/EMForm.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO events 
         (user_id, name, description, location, required_skills, urgency, event_date)
         VALUES (:uid, :name, :desc, :loc, :skills, :urgency, :date)"
    );
    $stmt->execute([
        ':uid'    => $_SESSION['user_id'],
        ':name'   => $eventName,
        ':desc'   => $eventDescription,
        ':loc'    => $location,
        ':skills' => json_encode($requiredSkills),
        ':urgency'=> $urgency,
        ':date'   => $eventDate,
    ]);

    header('Location: /pages/EMForm.php?success=1');
} catch (PDOException $e) {
    $_SESSION['errors'] = ['Failed to create event. Please try again.'];
    header('Location: /pages/EMForm.php');
}
exit;
