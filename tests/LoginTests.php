<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/helpers/auth_helpers.php';

class LoginTests extends TestCase
{
    public function testEmptyFields()
    {
        $result = validate_login('', '', []);
        $this->assertEquals("All fields are required.", $result);
    }

    public function testUserNotFound()
    {
        $users = ['someone@example.com' => ['password' => password_hash('password123', PASSWORD_DEFAULT)]];
        $result = validate_login('nonexistent@example.com', 'password123', $users);
        $this->assertEquals("User not found.", $result);
    }

    public function testIncorrectPassword()
    {
        $users = ['test@example.com' => ['password' => password_hash('correctpassword', PASSWORD_DEFAULT)]];
        $result = validate_login('test@example.com', 'wrongpassword', $users);
        $this->assertEquals("Incorrect password.", $result);
    }

    public function testSuccessfulLogin()
    {
        $users = ['test@example.com' => ['password' => password_hash('correctpassword', PASSWORD_DEFAULT)]];
        $result = validate_login('test@example.com', 'correctpassword', $users);
        $this->assertEquals("valid", $result);
    }
}