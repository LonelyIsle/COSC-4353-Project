<?php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json');


$servername = $host;
$username = $user;
$password = $pass;
$dbname = $db;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

if (!isset($_GET['user_email']) || empty($_GET['user_email'])) {
    http_response_code(400);
    die(json_encode(['error' => 'User Email is required.']));
}
$user_email = $_GET['user_email'];

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
        JOIN 
            UserCredentials AS uc ON vh.user_id = uc.user_id
        WHERE 
            uc.email = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

$stmt->bind_param("s", $user_email);

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