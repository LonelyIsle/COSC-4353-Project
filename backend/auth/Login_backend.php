<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /pages/Login.php");
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Email and password are required.';
    header("Location: /pages/Login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT user_id, email, password_hash, role
          FROM UserCredentials
         WHERE email = :email
         LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: /pages/Login.php");
        exit();
    }

    $_SESSION['user']    = $user['email'];
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role']    = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: /pages/EMForm.php");
    } else {
        header("Location: /pages/VolunteerMatching.php");
    }
    exit();

} catch (PDOException $e) {
    error_log("Login_backend error: " . $e->getMessage());
    $_SESSION['error'] = "Login failed. Please try again later.";
    header("Location: /pages/Login.php");
    exit();
}
