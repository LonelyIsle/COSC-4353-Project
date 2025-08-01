<?php
use PHPUnit\Framework\TestCase;
use Helpers\AuthHelper;

class LogoutTest extends TestCase
{
    public function testLogoutClearsSession()
    {
        // Ensure session is not active
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }

        // At this point, session_status() should be PHP_SESSION_NONE
        $_SESSION = []; // reset any lingering session data

        // Set session data and call logout
        session_start();
        $_SESSION['user'] = 'test@example.com';
        session_write_close(); // simulate ending session

        // Call logout function which should restart and clear session
        AuthHelper::logout_user();

        // Restart session to inspect contents
        session_start();
        $sessionData = $_SESSION;
        session_destroy();

        $this->assertArrayNotHasKey('user', $sessionData, "Session 'user' key should be removed after logout.");
    }
}