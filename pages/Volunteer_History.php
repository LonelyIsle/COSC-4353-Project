<?php

session_start();
if (($_SESSION['role'] ?? '') !== 'volunteer') {
    header('Location: /pages/Login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/css/global.css" />
    <meta charset="UTF-8">
    <title>Volunteer Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        header { background-color: #2e7d32; padding: 0.75rem 2rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; margin-top: 2rem; color: #333; }
        table { width: 80%; margin: 2rem auto; border-collapse: collapse; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 1rem; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
<?php
include __DIR__ . '/../components/navbar.php';
?>
    <h1 id="history">Your Volunteer History</h1>

    <table id="volunteerTable">
        <thead>
            <tr>
                <th>Event Date</th>
                <th>Event Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.querySelector('#volunteerTable tbody');

        async function fetchAndRenderVolunteerHistory() {
            tableBody.innerHTML = `<tr><td colspan="5">Loading your history...</td></tr>`;
            try {
                const response = await fetch('/backend/auth/get_volunteer_history.php');
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                const volunteerHistory = await response.json();
                tableBody.innerHTML = '';
                if (volunteerHistory.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5">No volunteer history found.</td></tr>`;
                    return;
                }
                volunteerHistory.forEach(record => {
                    const row = tableBody.insertRow();
                    row.innerHTML = `
                        <td>${record.event_date}</td>
                        <td>${record.event_name}</td>
                        <td>${record.description}</td>
                        <td>${record.location}</td>
                        <td>${record.status}</td>
                    `;
                });
            } catch (error) {
                console.error('Error fetching volunteer history:', error);
                tableBody.innerHTML = `<tr><td colspan="5">Failed to load history: ${error.message}</td></tr>`;
            }
        }

        fetchAndRenderVolunteerHistory();
    });
    </script>
</body>
</html>