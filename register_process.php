<?php
session_start();

$errors = [];

$first_name = trim(strip_tags($_POST['first_name'] ?? ''));
$last_name = trim(strip_tags($_POST['last_name'] ?? ''));
$city = trim(strip_tags($_POST['city'] ?? ''));
$state = trim(strip_tags($_POST['state'] ?? ''));
$zipcode = trim($_POST['zipcode'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($first_name === '' || !preg_match("/^[a-zA-Z ]+$/", $first_name)) {
    $errors[] = "Invalid first name.";
}
if ($last_name === '' || !preg_match("/^[a-zA-Z ]+$/", $last_name)) {
    $errors[] = "Invalid last name.";
}
if ($city === '' || !preg_match("/^[a-zA-Z ]+$/", $city)) {
    $errors[] = "Invalid city.";
}
if ($state === '' || !preg_match("/^[a-zA-Z ]+$/", $state)) {
    $errors[] = "Invalid state.";
}
if (!is_numeric($zipcode)) {
    $errors[] = "Invalid zipcode.";
}
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
    header("Location: register.php");
    exit();
}

// Redirect to home page after successful registration
header("Location: index.php");
exit();
?>