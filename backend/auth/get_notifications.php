<?php
// Start the session to access session variables
session_start();

header('Content-Type: application/json');

// --- DATABASE CONNECTION DETAILS ---
$servername = $host;
$username = $user;
$password = $pass;
$dbname = $db;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// --- GET USER EMAIL FROM SESSION ---
// Check if the user's email is stored in the session from logging in
if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    http_response_code(401); // Unauthorized
    die(json_encode(['error' => 'User not authenticated.']));
}
$user_email = $_SESSION['user_email'];


// --- PREPARE THE SQL SELECT STATEMENT ---
// The query now selects notifications for a specific user_email
$sql = "SELECT 
            notification_id,
            message,
            is_read,
            sent_at
        FROM 
            Notifications
        WHERE 
            user_email = ? AND is_read = 0
        ORDER BY
            sent_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

// Bind the user_email to the placeholder ("s" for string)
$stmt->bind_param("s", $user_email);

// --- EXECUTE THE QUERY AND FETCH RESULTS ---
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // We only need the message content for the dropdown
        $notifications[] = $row['message'];
    }
}

// --- CLOSE AND OUTPUT ---
$stmt->close();
$conn->close();

echo json_encode($notifications);
?>