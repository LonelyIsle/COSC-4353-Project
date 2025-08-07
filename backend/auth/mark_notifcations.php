<?php
session_start();
require_once __DIR__ . '/../db.php';
header('Content-Type: application/json'); // Tell the browser we're sending JSON

// Simulate success of marking notifications as read

//  we just return a success message.
echo json_encode(['status' => 'success', 'message' => 'Notifications marked as read.']);
?>