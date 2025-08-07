<?php
// backend/auth/submit_event.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/admin_dashboard.php?tab=create-event');
    exit;
}

$eventName        = trim($_POST['event_name']        ?? '');
$eventDescription = trim($_POST['event_description'] ?? '');
$location         = trim($_POST['location']          ?? '');
$requiredSkills   = $_POST['required_skills']       ?? [];
$urgency          = trim($_POST['urgency']          ?? '');
$eventDate        = trim($_POST['event_date']       ?? '');

$errors = [];
if ($eventName        === '') { $errors[] = 'Event Name is required.'; }
if ($eventDescription === '') { $errors[] = 'Event Description is required.'; }
if ($location         === '') { $errors[] = 'Location is required.'; }
if (empty($requiredSkills))   { $errors[] = 'Select at least one skill.'; }
if ($urgency          === '') { $errors[] = 'Urgency level is required.'; }
if ($eventDate        === '') { $errors[] = 'Event Date is required.'; }

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: /pages/admin_dashboard.php?tab=create-event');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO EventDetails
          (event_name, description, location, required_skills, urgency_level, event_date)
        VALUES
          (:name,       :desc,        :loc,
           :skills_json,:urgency,     :date)
    ");

    $stmt->execute([
        ':name'        => $eventName,
        ':desc'        => $eventDescription,
        ':loc'         => $location,
        ':skills_json' => json_encode($requiredSkills),
        ':urgency'     => $urgency,
        ':date'        => $eventDate,
    ]);

    header('Location: /pages/admin_dashboard.php?tab=create-event&success=1');
    exit;

} catch (PDOException $e) {
    error_log('submit_event.php error: ' . $e->getMessage());
    $_SESSION['errors'] = ['Failed to create event. Please try again.'];
    header('Location: /pages/admin_dashboard.php?tab=create-event');
    exit;
}
