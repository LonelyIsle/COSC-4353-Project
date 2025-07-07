<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/css/global.css">
</head>
<body class="centered-page">
    <div class="login-container">
         <a href="/index.php" class="button-link">Back</a>
        <h2>Login</h2>
        <form action="login_process.php" method="POST">
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