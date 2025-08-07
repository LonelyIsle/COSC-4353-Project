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

/**
 * Normalize skills JSON/CSV to array of lowercase strings.
 */
function parseSkillsToArray(?string $raw): array {
    if ($raw === null || $raw === '') return [];
    $skills = [];
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $skills = $decoded;
    } else {
        $skills = array_map('trim', explode(',', $raw));
    }
    $norm = [];
    foreach ($skills as $s) {
        if (!is_string($s)) continue;
        $s = trim($s);
        if ($s === '') continue;
        $norm[] = mb_strtolower($s);
    }
    return array_values(array_unique($norm));
}

function hasOverlap(array $a, array $b): bool {
    if (!$a || !$b) return false;
    $set = array_flip($b);
    foreach ($a as $x) {
        if (isset($set[$x])) return true;
    }
    return false;
}

// Fetch volunteer skills from UserProfile
$stmt = $pdo->prepare("SELECT skills FROM UserProfile WHERE user_id = ?");
$stmt->execute([$volunteerId]);
$volSkillsRaw = $stmt->fetchColumn();
$volSkills = parseSkillsToArray($volSkillsRaw);

// Fetch event required skills from EventDetails
$stmt = $pdo->prepare("SELECT required_skills, event_name FROM EventDetails WHERE event_id = ?");
$stmt->execute([$eventId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$eventSkills = parseSkillsToArray($row['required_skills'] ?? '');
$eventName   = $row['event_name'] ?? 'the selected event';

// Enforce: if event lists required skills, volunteer must have at least one
if (!empty($eventSkills) && !hasOverlap($volSkills, $eventSkills)) {
    $_SESSION['errors'] = ['This volunteer does not meet the required skills for the selected event.'];
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}

try {
    // Record the match
    $stmt = $pdo->prepare("
        INSERT INTO VolunteerHistory (user_id, event_id, status)
        VALUES (:uid, :eid, 'registered')
    ");
    $stmt->execute([
        ':uid' => $volunteerId,
        ':eid' => $eventId,
    ]);

    // Notify the volunteer
    $message = "You have been matched with the event: " . $eventName;
    $stmt = $pdo->prepare("
        INSERT INTO Notifications (user_id, message, is_read, sent_at)
        VALUES (:uid, :msg, 0, NOW())
    ");
    $stmt->execute([
        ':uid' => $volunteerId,
        ':msg' => $message,
    ]);

    header('Location: /pages/admin_dashboard.php?tab=volunteer-match&successvm=1');
    exit;

} catch (PDOException $e) {
    error_log('process_match error: ' . $e->getMessage());
    $_SESSION['errors'] = ['Failed to match volunteer. Please try again.'];
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}
