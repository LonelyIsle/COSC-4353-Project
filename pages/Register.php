<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="/css/global.css">
</head>
<body class="centered-page">
    <div class="register-container">
        <h2>Register</h2>
        <?php if (!empty($_SESSION['errors'])): ?>
        <div class="error-box">
          <strong>Please correct the following errors:</strong>
          <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php unset($_SESSION['errors']); endif; ?>
        <form action="/register_process.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>

            <label>Password:</label>
            <input type="password" name="password" required minlength="6">

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required minlength="6">

            <button type="submit">Register</button>
        </form>
         <p class="centered-link">Wanna go back home?</p>
        <a href="/index.php" class="button-link">Homepage</a>
        <?php unset($_SESSION['old']); ?>
    </div>
</body>
</html>