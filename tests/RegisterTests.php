<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/helpers/auth_helpers.php';

class RegisterTests extends TestCase
{
    public function testEmptyFields()
    {
        $result = validate_registration('', '', '', []);
        $this->assertEquals("All fields are required.", $result);
    }

    public function testShortPassword()
    {
        $result = validate_registration('test@example.com', '123', '123', []);
        $this->assertEquals("Password must be at least 6 characters.", $result);
    }

    public function testPasswordMismatch()
    {
        $result = validate_registration('test@example.com', '123456', '654321', []);
        $this->assertEquals("Passwords do not match.", $result);
    }

    public function testUserAlreadyExists()
    {
        $existing = ['test@example.com' => ['password' => 'hashed']];
        $result = validate_registration('test@example.com', '123456', '123456', $existing);
        $this->assertEquals("User already exists.", $result);
    }

    public function testValidRegistration()
    {
        $result = validate_registration('new@example.com', '123456', '123456', []);
        $this->assertEquals("valid", $result);
    }
}