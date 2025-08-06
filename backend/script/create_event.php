<?php
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

require_once __DIR__ . '/../db.php';

function clean(string $key): string {
    return trim($_POST[$key] ?? '');
}

$successMsg = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = clean('event_name');
    $desc    = clean('description');
    $loc     = clean('location');
    $skills  = clean('required_skills');
    $urgency = clean('urgency_level');
    $date    = clean('event_date');

    if ($name === '')         $errors[] = 'Event name is required.';
    if ($desc === '')         $errors[] = 'Description is required.';
    if ($loc === '')          $errors[] = 'Location is required.';
    if ($skills === '')       $errors[] = 'Required skills must be provided.';
    if (!is_numeric($urgency) || $urgency < 1 || $urgency > 5)
                               $errors[] = 'Urgency must be a number between 1 and 5.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
                               $errors[] = 'Event date must be in YYYY-MM-DD format.';

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO EventDetails 
                (event_name, description, location, required_skills, urgency_level, event_date)
                VALUES 
                (:name, :desc, :loc, :skills, :urgency, :date)"
            );

            $stmt->execute([
                ':name'    => $name,
                ':desc'    => $desc,
                ':loc'     => $loc,
                ':skills'  => $skills,
                ':urgency' => $urgency,
                ':date'    => $date,
            ]);

            $successMsg = 'Event created successfully!';
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<div class="event-container">
    <h2>Create New Event</h2>

    <?php if ($successMsg): ?>
        <div class="error-box" style="background: #ddffdd; border-color: #2e7d32; color: #2e7d32;">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="event_name">Event Name</label>
            <input type="text" name="event_name" id="event_name" required value="<?= htmlspecialchars($name ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($desc ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" id="location" required value="<?= htmlspecialchars($loc ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="required_skills">Required Skills (comma separated)</label>
            <input type="text" name="required_skills" id="required_skills" required value="<?= htmlspecialchars($skills ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="urgency_level">Urgency Level</label>
            <input type="text" name="urgency_level" id="urgency_level" required value="<?= htmlspecialchars($urgency ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="event_date">Event Date</label>
            <input type="date" name="event_date" id="event_date" required value="<?= htmlspecialchars($date ?? '') ?>">
        </div>

        <button type="submit">Create Event</button>
    </form>
</div>
