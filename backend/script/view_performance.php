<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$rows = [];
$error = '';

try {
    $stmt = $pdo->query("
        SELECT e.event_name, u.full_name, vh.status
        FROM VolunteerHistory vh
        JOIN UserProfile u ON vh.user_id = u.user_id
        JOIN EventDetails e ON vh.event_id = e.event_id
        ORDER BY e.event_date DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="performance-container">
    <h2>Volunteer Performance Report</h2>

    <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="performance-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Volunteer</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No performance data available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .performance-container {
        max-width: 900px;
        margin: 40px auto;
        background: #fff8e1;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .performance-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .performance-table th,
    .performance-table td {
        padding: 12px 16px;
        border: 1px solid #2e7d32;
        text-align: left;
    }

    .performance-table th {
        background-color: #2e7d32;
        color: white;
    }

    .performance-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .performance-table tr:hover {
        background-color: #e0f2f1;
    }
</style>
