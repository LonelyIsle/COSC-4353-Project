<?php
session_start();

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

// Redirect to home page after successful registration
$_SESSION['success'] = "Registration successful!";
header("Location: /index.php");
exit();
?>