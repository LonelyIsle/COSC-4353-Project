<?php
require_once __DIR__ . '/../db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$events = $pdo->query("SELECT event_id, event_name FROM EventDetails ORDER BY event_date ASC")->fetchAll(PDO::FETCH_ASSOC);
$volunteers = $pdo->query("SELECT user_id, full_name FROM UserProfile ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

$where = [];
$params = [];

if (!empty($_GET['event'])) {
    $where[] = "vh.event_id = :event_id";
    $params[':event_id'] = $_GET['event'];
}

if (!empty($_GET['volunteer'])) {
    $where[] = "vh.user_id = :volunteer_id";
    $params[':volunteer_id'] = $_GET['volunteer'];
}

$query = "
  SELECT e.event_name, u.full_name, vh.status, vh.comments, ea.name AS assignment_name
  FROM VolunteerHistory vh
  JOIN UserProfile u ON vh.user_id = u.user_id
  JOIN EventDetails e ON vh.event_id = e.event_id
  LEFT JOIN EventAssignments ea ON ea.event_id = e.event_id AND ea.user_id = u.user_id
";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY e.event_date DESC";

$rows = [];
$error = '';

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="performance-container">
    <h2>Volunteer Performance Report</h2>

    <form method="GET" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
        <div class="form-group">
            <label for="event_filter"><strong>Filter by Event:</strong></label>
            <select name="event" id="event_filter">
                <option value="">All Events</option>
                <?php foreach ($events as $e): ?>
                    <option value="<?= $e['event_id'] ?>" <?= isset($_GET['event']) && $_GET['event'] == $e['event_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['event_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="volunteer_filter"><strong>Filter by Volunteer:</strong></label>
            <select name="volunteer" id="volunteer_filter">
                <option value="">All Volunteers</option>
                <?php foreach ($volunteers as $v): ?>
                    <option value="<?= $v['user_id'] ?>" <?= isset($_GET['volunteer']) && $_GET['volunteer'] == $v['user_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($v['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="filter-button">Apply Filters</button>
    </form>

    <?php if ($error): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <table class="performance-table">
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Volunteer</th>
                <th>Status</th>
                <th>Assignment</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['assignment_name']) ?></td>
                        <td><?= htmlspecialchars($row['comments']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No performance data available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .performance-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff8e1;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    form .form-group {
        display: flex;
        flex-direction: column;
    }

    .filter-button {
        height: fit-content;
        padding: 6px 12px;
        margin-top: 20px;
        background-color: #fff;
        color: #2e7d32;
        border: 1px solid #2e7d32;
        border-radius: 4px;
        cursor: pointer;
    }

    .filter-button:hover {
        background-color: #e8f5e9;
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
