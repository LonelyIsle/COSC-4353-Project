<?php
// --- SETUP ---
header('Content-Type: application/json'); // Tell the browser we're sending JSON

// --- DATABASE CONNECTION DETAILS ---
$servername = $host;
$username = $user;
$password = $pass;
$dbname = $db; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    // Set HTTP response code to 500 Internal Server Error
    http_response_code(500);
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// --- GET USER ID FROM REQUEST ---
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    // Set HTTP response code to 400 Bad Request
    http_response_code(400);
    die(json_encode(['error' => 'User ID is required.']));
}
$user_id = $_GET['user_id'];


// --- PREPARE THE SQL SELECT STATEMENT ---
// This query joins VolunteerHistory and EventDetails to get all relevant info for a specific user.
$sql = "SELECT 
            vh.id AS history_id,
            vh.status,
            ed.event_name,
            ed.description,
            ed.location,
            ed.required_skills,
            ed.urgency_level,
            ed.event_date
        FROM 
            VolunteerHistory AS vh
        JOIN 
            EventDetails AS ed ON vh.event_id = ed.event_id
        WHERE 
            vh.user_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]));
}

// Bind the user_id to the placeholder. "i" means the variable is an integer.
$stmt->bind_param("i", $user_id);

// --- EXECUTE THE QUERY AND FETCH RESULTS ---
$stmt->execute();
$result = $stmt->get_result();

$volunteerHistory = [];
if ($result && $result->num_rows > 0) {
    // Fetch all results into an associative array
    $volunteerHistory = $result->fetch_all(MYSQLI_ASSOC);
}

// --- CLOSE THE STATEMENT AND CONNECTION ---
$stmt->close();
$conn->close();

// --- OUTPUT THE DATA AS JSON ---
// This will output the array of history events, or an empty array if none were found.
echo json_encode($volunteerHistory);

?>
