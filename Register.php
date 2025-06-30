<?php session_start();?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
        .register-container {
            background: #000;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            text-align: center;
            width: 320px;
        }
        h2 {
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
            text-align: left;
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
            margin-top: 15px;
            padding: 8px 20px;
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
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ff6f61;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form action="register_process.php" method="POST">
            <label>First Name:</label>
            <input type="text" name="first_name" required>

            <label>Last Name:</label>
            <input type="text" name="last_name" required>

            <label>City:</label>
            <input type="text" name="city" required>

            <label>State:</label>
            <input type="text" name="state" required>

            <label>Zipcode:</label>
            <input type="text" name="zipcode" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>