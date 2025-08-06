<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$events = [];
$error = '';

try {
    $stmt = $pdo->query("SELECT * FROM EventDetails ORDER BY event_date DESC");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>

<div class="event-container">
    <h2>Event Timeline</h2>

    <?php if ($error): ?>
        <div class="error-box">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($events && count($events) > 0): ?>
        <ul class="timeline-list">
            <?php foreach ($events as $row): ?>
                <li class="timeline-item">
                    <h3><?= htmlspecialchars($row['event_name']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($row['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p><strong>Required Skills:</strong> <?= htmlspecialchars($row['required_skills']) ?></p>
                    <p><strong>Urgency:</strong> <?= htmlspecialchars($row['urgency_level']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No events found.</p>
    <?php endif; ?>
</div>

<style>
    .timeline-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .timeline-item {
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #2e7d32;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .timeline-item h3 {
        margin-bottom: 10px;
        color: #2e7d32;
    }
</style>
