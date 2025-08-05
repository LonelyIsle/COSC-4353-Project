<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        header { background-color: #2e7d32; padding: 0.75rem 2rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .navbar { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; position: relative; }
        .nav-left a, .nav-right a { color: #fff; margin-right: 1.5rem; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .nav-left a:last-child { margin-right: 0; }
        .nav-left a:hover, .nav-right a:hover { color: #c8e6c9; }
        #notificationIcon { font-size: 1.3rem; position: relative; cursor: pointer; }
        .badge { position: absolute; top: -5px; right: -10px; background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; display: none; }
        .badge.visible { display: inline; }
        .dropdown { display: none; position: absolute; right: 0; top: 2.8rem; background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-radius: 6px; width: 280px; z-index: 1000; overflow: hidden; }
        .dropdown.active { display: block; }
        .dropdown ul { list-style: none; margin: 0; padding: 0; }
        .dropdown li { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; font-size: 0.9rem; color: #333; }
        .dropdown li:last-child { border-bottom: none; }
        .dropdown .no-notifications { color: #999; text-align: center; }
        h1 { text-align: center; margin-top: 2rem; color: #333; }
        .search-container { text-align: center; margin: 2rem auto; padding: 1rem; background-color: #fff; width: 80%; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); border-radius: 8px; }
        .search-container label { margin-right: 10px; font-weight: bold; }
        .search-container input { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 250px; }
        .search-container button { padding: 8px 15px; border: none; background-color: #2e7d32; color: white; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease; }
        .search-container button:hover { background-color: #1b5e20; }
        table { width: 80%; margin: 2rem auto; border-collapse: collapse; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 1rem; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="index.php">Home</a>
                <a href="#history">Volunteer History</a>
            </div>
            <div class="nav-right">
                <a id="notificationIcon">🔔<span class="badge" id="notificationBadge"></span></a>
                <div class="dropdown" id="notificationDropdown">
                    <ul id="notificationList"></ul>
                </div>
            </div>
        </nav>
    </header>

    <h1 id="history">Volunteer History</h1>

    <div class="search-container">
        <label for="userEmailInput">Enter Volunteer Email:</label>
        <input type="email" id="userEmailInput" placeholder="e.g., volunteer@example.com">
        <button id="fetchHistoryBtn">Get History</button>
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
        <tbody></tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.querySelector('#volunteerTable tbody');
            const userEmailInput = document.getElementById('userEmailInput');
            const fetchHistoryBtn = document.getElementById('fetchHistoryBtn');
            const notificationIcon = document.getElementById('notificationIcon');
            const dropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');

            async function fetchAndRenderVolunteerHistory(userEmail) {
                tableBody.innerHTML = `<tr><td colspan="5">Loading...</td></tr>`;
                try {
                    const response = await fetch(`backend/auth/get_volunteer_history.php?user_email=${encodeURIComponent(userEmail)}`);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                    }
                    const volunteerHistory = await response.json();
                    tableBody.innerHTML = '';
                    if (volunteerHistory.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="5">No volunteer history found for this email.</td></tr>`;
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
                    const response = await fetch('backend/auth/get_notifications.php');
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
                    await fetch('backend/auth/mark_notifications_read.php', { method: 'POST' });
                    fetchAndRenderNotifications();
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                }
            }

            fetchHistoryBtn.addEventListener('click', () => {
                const userEmail = userEmailInput.value.trim();
                if (userEmail) {
                    fetchAndRenderVolunteerHistory(userEmail);
                } else {
                    alert('Please enter a Volunteer Email.');
                }
            });

            userEmailInput.addEventListener('keyup', (event) => {
                if (event.key === 'Enter') {
                    fetchHistoryBtn.click();
                }
            });

            notificationIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (dropdown.classList.contains('active') && !notificationIcon.contains(e.target) && !dropdown.contains(e.target)) {
                    if (notificationBadge.classList.contains('visible')) {
                        markNotificationsAsRead();
                    }
                    dropdown.classList.remove('active');
                }
            });

            fetchAndRenderNotifications();
        });
    </script>
</body>
</html>