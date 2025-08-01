<?php
namespace Helpers;

class AuthHelper {
    public static function validate_registration($email, $password, $confirm_password, $existing_users) {
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

    public static function validate_login($email, $password, $pdo) {
        if (empty($email) || empty($password)) {
            return "All fields are required.";
        }

        $stmt = $pdo->prepare("SELECT password_hash FROM UserCredentials WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return "User not found.";
        }

        if (!password_verify($password, $user['password_hash'])) {
            return "Incorrect password.";
        }

        return "valid";
    }

    public static function logout_user() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }
}