<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT user_id, email, password_hash, role FROM UserCredentials WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("Submitted email: $email");
        error_log("Submitted password: $password");
        error_log("Stored password hash: " . ($user['password'] ?? 'not found'));

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user['email'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: /pages/admin_dashboard.php");
            } else {
                header("Location: /pages/volunteer_dashboard.php");
            }
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