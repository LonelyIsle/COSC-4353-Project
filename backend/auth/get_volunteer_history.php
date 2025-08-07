<?php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');

// --- CHECK IF USER IS LOGGED IN ---
// Ensure the user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    die(json_encode(['error' => 'User not authenticated. Please log in.']));
}

// Get the logged-in volunteer's ID from the session
$volunteer_id = $_SESSION['user_id'];


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


$sql = "SELECT 
            ed.event_date,
            ed.event_name,
            ed.description,
            ed.location,
            vh.status
        FROM 
            VolunteerHistory AS vh
        JOIN 
            EventDetails AS ed ON vh.event_id = ed.event_id
        WHERE 
            vh.user_id = ?
        ORDER BY
            ed.event_date DESC"; // Order by most recent event

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

// Bind the integer user_id from the session ("i" for integer)
$stmt->bind_param("i", $volunteer_id);

$stmt->execute();
$result = $stmt->get_result();

$volunteerHistory = [];
if ($result && $result->num_rows > 0) {
   
    $volunteerHistory = $result->fetch_all(MYSQLI_ASSOC);
}

$stmt->close();
$conn->close();

echo json_encode($volunteerHistory);
?>