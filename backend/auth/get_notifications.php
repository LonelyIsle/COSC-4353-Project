<?php
header('Content-Type: application/json'); // Tell the browser we're sending JSON

// Simulated hardcoded notifications data
$notifications = [
    "New event added: Park Cleanup on Aug 5th",
    "Your hours for 'Animal Shelter Support' were approved.",
    "Reminder: Community Garden setup is this Saturday."
];

// Output the data as JSON
echo json_encode($notifications);
?>