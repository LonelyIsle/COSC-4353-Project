<?php
session_start();
require_once __DIR__ . '/../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT user_id, email, password, role FROM UserCredentials WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['email'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            header("Location: /pages/profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: /pages/Login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Login failed. Please try again later.";
        header("Location: /pages/Login.php");
        exit();
    }
} else {
    header("Location: /pages/Login.php");
    exit();
}