<?php
session_start();
session_unset(); // optional: clears all session variables
session_destroy(); // destroys the session

// Optional: clear session cookie
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Redirect to home or login page
header("Location: /index.php");
exit();
?>