<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body class="centered-page">
    <div class="register-container">
        <h2>Register</h2>
        <?php if (!empty($_SESSION['errors'])): ?>
        <div style="background:#ffdddd; border:1px solid #ff6f61; padding:15px; border-radius:5px; color:#333; max-width:400px; margin:20px auto;">
          <strong>Please correct the following errors:</strong>
          <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php unset($_SESSION['errors']); endif; ?>
        <form action="register_process.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
         <p class="centered-link">Wanna go back home?</p>
        <a href="index.php"><button>Homepage</button></a>
        <?php unset($_SESSION['old']); ?>
    </div>
</body>
</html>