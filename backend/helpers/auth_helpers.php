<?php
function validate_registration($email, $password, $confirm_password, $existing_users) {
    if (empty($email) || empty($password) || empty($confirm_password)) {
        return "All fields are required.";
    }

    if (strlen($password) < 6) {
        return "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        return "Passwords do not match.";
    }

    if (array_key_exists($email, $existing_users)) {
        return "User already exists.";
    }

    return "valid";
}
function validate_login($email, $password, $users) {
    if (empty($email) || empty($password)) {
        return "All fields are required.";
    }

    if (!isset($users[$email])) {
        return "User not found.";
    }

    if (!password_verify($password, $users[$email]['password'])) {
        return "Incorrect password.";
    }

    return "valid";
}

function logout_user() {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear session variables
    $_SESSION = [];

    // Destroy the session
    session_destroy();
}