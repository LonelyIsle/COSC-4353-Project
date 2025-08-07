<?php
// Start the session to access session variables
session_start();
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

// --- CHECK FOR AUTHENTICATED USER ---
// First, ensure the user is logged in and the user_id is in the session
if (!isset($_SESSION['user_id'])) {
    // Send an "Unauthorized" response code if the user is not logged in
    http_response_code(401); 
    echo json_encode(['error' => 'User not authenticated.']);
    exit;
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];


// --- DATABASE CONNECTION ---
$servername = $host;
$username = $user;
$password = $pass;
$dbname = $db;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// --- PREPARE THE SQL SELECT STATEMENT ---
// This query is now simpler and more efficient. It gets notifications for the logged-in user.
$sql = "SELECT 
            message
        FROM 
            Notifications
        WHERE 
            user_id = ? AND is_read = 0
        ORDER BY
            sent_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

// Bind the integer user_id from the session ("i" for integer)
$stmt->bind_param("i", $user_id);

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