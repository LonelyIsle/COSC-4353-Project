<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
<style>
        body {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #000;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            text-align: left;
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #ff6f61;
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #ff6f61;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ff6f61;
            border-radius: 4px;
            background: #000;
            color: #ff6f61;
        }
        button {
            width: auto;
            display: block;
            margin: 15px auto 0 auto;
            padding: 6px 18px;
            background-color: #ff6f61;
            color: #000;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #ff8a75;
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ff6f61;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login_process.php" method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p class="register-link">Don't have an account?</p>
        <a href="register.php"><button>Register</button></a>
    </div>
</body>
</html>