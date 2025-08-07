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

$adminId     = (int)$_SESSION['user_id'];
$volunteerId = (int)($_POST['volunteer_name'] ?? 0);
$eventId     = (int)($_POST['matched_event']  ?? 0);

$errors = [];
if ($eventId     <= 0) { $errors[] = 'Event selection is required.'; }
if ($volunteerId <= 0) { $errors[] = 'Volunteer is required.'; }

if ($errors) {
    $_SESSION['vm_errors'] = $errors;
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}

/** Parse JSON/CSV/bracketed skills to lowercase array */
function parseSkillsToArray(?string $raw): array {
    if ($raw === null) return [];
    $raw = trim($raw);
    if ($raw === '') return [];
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $skills = $decoded;
    } else {
        if ($raw[0] === '[' && substr($raw, -1) === ']') {
            $inner = trim(substr($raw, 1, -1));
            $coerced = '[' . str_replace("'", '"', $inner) . ']';
            $decoded2 = json_decode($coerced, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                $skills = $decoded2;
            } else {
                $skills = array_map('trim', explode(',', $inner));
            }
        } else {
            $skills = array_map('trim', explode(',', $raw));
        }
    }
    $out = [];
    foreach ($skills as $s) {
        if (!is_string($s)) continue;
        $s = trim($s);
        if ($s !== '') $out[] = mb_strtolower($s);
    }
    return array_values(array_unique($out));
}

function hasOverlap(array $a, array $b): bool {
    if (!$a || !$b) return false;
    $set = array_flip($b);
    foreach ($a as $x) if (isset($set[$x])) return true;
    return false;
}

// Load volunteer skills
$stmt = $pdo->prepare("SELECT skills FROM UserProfile WHERE user_id = ?");
$stmt->execute([$volunteerId]);
$volSkills = parseSkillsToArray($stmt->fetchColumn() ?: '');

// Load event info (skills, name, description)
$stmt = $pdo->prepare("
    SELECT required_skills, event_name, description
      FROM EventDetails
     WHERE event_id = ?
");
$stmt->execute([$eventId]);
$eventRow   = $stmt->fetch(PDO::FETCH_ASSOC);
$eventSkills= parseSkillsToArray($eventRow['required_skills'] ?? '');
$eventName  = $eventRow['event_name'] ?? 'Selected Event';
$eventDesc  = $eventRow['description'] ?? null;

// Enforce skill requirement when the event lists any
if (!empty($eventSkills) && !hasOverlap($volSkills, $eventSkills)) {
    $_SESSION['vm_errors'] = ['This volunteer does not meet the required skills for the selected event.'];
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}

try {
    $pdo->beginTransaction();

    // ✅ Insert into EventAssignments including name & description from EventDetails
    $stmt = $pdo->prepare("
        INSERT INTO EventAssignments (user_id, event_id, assigned_by, assigned_at, name, description)
        VALUES (:uid, :eid, :aid, NOW(), :name, :desc)
    ");
    $stmt->execute([
        ':uid'  => $volunteerId,
        ':eid'  => $eventId,
        ':aid'  => $adminId,
        ':name' => $eventName,
        ':desc' => $eventDesc,
    ]);

    // Optional notification
    $stmt = $pdo->prepare("
        INSERT INTO Notifications (user_id, message, is_read, sent_at)
        VALUES (:uid, :msg, 0, NOW())
    ");
    $stmt->execute([
        ':uid' => $volunteerId,
        ':msg' => "You have been matched with the event: " . $eventName,
    ]);

    $pdo->commit();
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match&successvm=1');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('process_match error: ' . $e->getMessage());
    $_SESSION['vm_errors'] = ['Failed to match volunteer. Please try again.'];
    header('Location: /pages/admin_dashboard.php?tab=volunteer-match');
    exit;
}
