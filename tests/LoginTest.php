<?php
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


require_once __DIR__ . '/../backend/helpers/auth_helpers.php';
use Helpers\AuthHelper;

class LoginTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        require __DIR__ . '/../backend/db.php';
        $this->pdo = $pdo;
    }
    public function testEmptyFields()
    {
        $result = AuthHelper::validate_login('', '', $this->pdo);
        $this->assertEquals("All fields are required.", $result);
    }

    public function testUserNotFound()
    {
        $result = AuthHelper::validate_login('nonexistent@example.com', 'somepassword', $this->pdo);
        $this->assertEquals("User not found.", $result);
    }

    public function testIncorrectPassword()
    {
        $result = AuthHelper::validate_login('testuser@example.com', 'wrongpassword', $this->pdo);
        $this->assertEquals("Incorrect password.", $result);
    }

    public function testSuccessfulLogin()
    {
        $result = AuthHelper::validate_login('testuser@example.com', 'correctpassword', $this->pdo);
        $this->assertEquals("valid", $result);
    }

    public function testAdminLogin()
    {
        $result = AuthHelper::validate_login('admin@example.com', 'adminpass', $this->pdo);
        $this->assertEquals("valid", $result); // Adjust if role is returned
    }

    public function testVolunteerLogin()
    {
        $result = AuthHelper::validate_login('volunteer@example.com', 'volunteerpass', $this->pdo);
        $this->assertEquals("valid", $result); // Adjust if role is returned
    }
}