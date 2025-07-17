<?php
session_start();

// Simulated user data
$valid_users = [
    'user@example.com' => 'password123',
    'manager@domain.com' => 'managerpass'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (isset($valid_users[$email]) && $valid_users[$email] === $password) {
        $_SESSION['user'] = $email;

        header("Location: /pages/profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: /pages/Login.php");
        exit();
    }
} else {
    header("Location: /pages/Login.php");
    exit();
}