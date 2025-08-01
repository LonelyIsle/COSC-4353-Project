<?php
session_start();

require_once __DIR__ . '/../db_connection.php'; 

$errors = [];

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if (!$email) {
    $errors[] = "Invalid email address.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header("Location: /pages/register.php");
    exit();
}

try {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO UserCredentials (email, password) VALUES (:email, :password)");
    $stmt->execute([
        ':email' => $email,
        ':password' => $hashedPassword
    ]);
} catch (PDOException $e) {
    $_SESSION['errors'] = ["Registration failed: " . $e->getMessage()];
    $_SESSION['old'] = $_POST;
    header("Location: /pages/register.php");
    exit();
}

// Redirect to home page after successful registration
$_SESSION['success'] = "Registration successful!";
header("Location: /index.php");
exit();
?>