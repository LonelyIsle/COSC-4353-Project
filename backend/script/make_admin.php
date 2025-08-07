<?php
require_once __DIR__ . '/../db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$successMsg = '';
$errors = [];
$volunteers = [];

try {
    $stmt = $pdo->query("SELECT user_id, email FROM UserCredentials WHERE role != 'admin'");
    $volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error loading users: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];

    if ($userId <= 0) {
        $errors[] = "Invalid user selected.";
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("UPDATE UserCredentials SET role = 'admin' WHERE user_id = :id");
            $stmt->execute([':id' => $userId]);
            $successMsg = "User promoted to admin successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="event-container">
    <h2>Promote Volunteer to Admin</h2>

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
            <label for="user_id">Select a Volunteer</label>
            <select name="user_id" id="user_id" required>
                <option value="">Select user</option>
                <?php foreach ($volunteers as $user): ?>
                    <option value="<?= htmlspecialchars($user['user_id']) ?>">
                        <?= htmlspecialchars($user['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Promote</button>
    </form>
</div>
