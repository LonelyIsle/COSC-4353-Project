<?php
header('Content-Type: application/json'); // Tell the browser we're sending JSON

// Simulated hardcoded volunteer history data
$volunteerHistory = [
    [
        'date' => '2024-01-15',
        'event' => 'Beach Cleanup',
        'hours' => 5,
        'description' => 'Collected trash and debris'
    ],
    [
        'date' => '2024-03-22',
        'event' => 'Food Drive',
        'hours' => 3,
        'description' => 'Packed and distributed food'
    ],
    [
        'date' => '2024-05-10',
        'event' => 'Tree Planting',
        'hours' => 4,
        'description' => 'Planted trees in the local park'
    ],
    [
        'date' => '2025-06-01', // Future date, as in your original
        'event' => 'Animal Shelter Support',
        'hours' => 6,
        'description' => 'Assisted with animal care and cleaning'
    ]
];

// Output the data as JSON
echo json_encode($volunteerHistory);
?>