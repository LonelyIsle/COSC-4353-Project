<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/css/global.css">
</head>
<body class="centered-page">
    <div class="login-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-box">
                <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
         <a href="/index.php" class="button-link">Back</a>
        <h2>Login</h2>
        <form action="/backend/auth/Login_backend.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p class="centered-link">Don't have an account?</p>
        <a href="register.php"><button>Register</button></a>
    </div>
</body>
</html>