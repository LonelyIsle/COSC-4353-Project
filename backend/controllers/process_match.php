<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}

$volunteerId = intval($_POST['volunteer_name'] ?? 0);
$eventId     = intval($_POST['matched_event']  ?? 0);

$errors = [];
if ($volunteerId <= 0) { $errors[] = 'Volunteer is required.'; }
if ($eventId     <= 0) { $errors[] = 'Event selection is required.'; }

if ($errors) {
    $_SESSION['errors'] = $errors;
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO VolunteerHistory (user_id, event_id, status)
        VALUES (:uid, :eid, 'registered')
    ");
    $stmt->execute([
        ':uid' => $volunteerId,
        ':eid' => $eventId,
    ]);

    $stmt = $pdo->prepare("SELECT event_name FROM EventDetails WHERE event_id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($event) {
        $message = "You have been matched with the event: " . $event['event_name'];

        // Insert notification
        $stmt = $pdo->prepare("
            INSERT INTO Notifications (user_id, message, is_read, sent_at)
            VALUES (:uid, :msg, 0, NOW())
        ");
        $stmt->execute([
            ':uid' => $volunteerId,
            ':msg' => $message,
        ]);
    }

    header('Location: /pages/admin_dashboard.php?tab=volunteer-match&success=1');
    exit;

} catch (PDOException $e) {
    error_log('process_match error: ' . $e->getMessage());
    $_SESSION['errors'] = ['Failed to match volunteer. Please try again.'];
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}
