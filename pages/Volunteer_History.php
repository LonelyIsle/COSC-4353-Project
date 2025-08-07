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
        
        .page-notification-container {
            width: 80%;
            margin: 1rem auto -1rem;
            text-align: right;
            position: relative;
        }
        #pageNotificationBtn {
            font-size: 1.3rem;
            position: relative;
            cursor: pointer;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        #pageNotificationBtn:hover {
            background-color: #f2f2f2;
        }

        .badge { position: absolute; top: -5px; right: -10px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; display: none; }
        .badge.visible { display: inline; }
        .dropdown { display: none; position: absolute; right: 0; top: 2.8rem; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 6px; width: 280px; z-index: 1000; overflow: hidden; }
        .dropdown.active { display: block; }
        .dropdown ul { list-style: none; margin: 0; padding: 0; }
        .dropdown li { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; font-size: 0.9rem; color: #333; }
        .dropdown li:last-child { border-bottom: none; }
        .dropdown .no-notifications { color: #999; text-align: center; }
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

    <div class="page-notification-container">
        <button id="pageNotificationBtn" title="Notifications">
            🔔
            <span id="notificationBadge" class="badge"></span>
        </button>
        <div id="notificationDropdown" class="dropdown">
            <ul id="notificationList"></ul>
        </div>
    </div>

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
        
        // REMOVED: Constants for the old search box are gone.
        
        const pageNotificationBtn = document.getElementById('pageNotificationBtn');
        const dropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');

        // MODIFIED: The function no longer needs an email argument.
        async function fetchAndRenderVolunteerHistory() {
            tableBody.innerHTML = `<tr><td colspan="5">Loading your history...</td></tr>`;
            try {
                // MODIFIED: The fetch URL is simpler as the backend uses the session.
                const response = await fetch('/backend/auth/get_volunteer_history.php');
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                const volunteerHistory = await response.json();
                tableBody.innerHTML = '';
                if (volunteerHistory.length === 0) {
                    // MODIFIED: More generic message.
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
        
        async function fetchAndRenderNotifications() {
            notificationList.innerHTML = '';
            notificationBadge.classList.remove('visible');
            try {
                const response = await fetch('/backend/auth/get_notifications.php');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const notifications = await response.json();

                if (notifications.length > 0) {
                    notificationBadge.textContent = notifications.length;
                    notificationBadge.classList.add('visible');
                    notifications.forEach(note => {
                        const li = document.createElement('li');
                        li.textContent = note;
                        notificationList.appendChild(li);
                    });
                } else {
                    notificationList.innerHTML = '<li class="no-notifications">No new notifications</li>';
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                notificationList.innerHTML = '<li class="no-notifications">Error loading notifications.</li>';
            }
        }

        async function markNotificationsAsRead() {
            try {
                await fetch('/backend/auth/mark_notifications_read.php', { method: 'POST' });
                fetchAndRenderNotifications();
            } catch (error) {
                console.error('Error marking notifications as read:', error);
            }
        }

        // REMOVED: Event listeners for the search button and input field are gone.

        pageNotificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', (e) => {
            if (dropdown.classList.contains('active') && !pageNotificationBtn.contains(e.target) && !dropdown.contains(e.target)) {
                if (notificationBadge.classList.contains('visible')) {
                    markNotificationsAsRead();
                }
                dropdown.classList.remove('active');
            }
        });

        // --- AUTOMATICALLY LOAD DATA ---
        // Both functions are now called when the page loads.
        fetchAndRenderNotifications();
        fetchAndRenderVolunteerHistory();
    });
    </script>
</body>
</html>