<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        
        header {
            background-color: #2e7d32;
            padding: 0.75rem 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .nav-left a,
        .nav-right a {
            color: #fff;
            margin-right: 1.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-left a:last-child {
            margin-right: 0;
        }

        .nav-left a:hover,
        .nav-right a:hover {
            color: #c8e6c9;
        }

        #notificationIcon {
            font-size: 1.3rem;
            position: relative;
            cursor: pointer;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            display: none; /* Hide badge by default */
        }

        .badge.visible {
            display: inline; /* Show badge when notifications exist */
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 2.8rem;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            width: 280px;
            z-index: 1000;
            overflow: hidden;
        }

        .dropdown.active {
            display: block;
        }

        .dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .dropdown li {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
            color: #333;
        }

        .dropdown li:last-child {
            border-bottom: none;
        }
        
        .dropdown .no-notifications {
            color: #999;
            text-align: center;
        }

        h1 {
            text-align: center;
            margin-top: 2rem;
            color: #333;
        }

        table {
            width: 80%;
            margin: 2rem auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 1rem;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-left">
                <a href="index.php">Home</a>
                <a href="#history">Volunteer History</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </div>
            <div class="nav-right">
                <a id="notificationIcon">🔔<span class="badge" id="notificationBadge"></span></a>
                <div class="dropdown" id="notificationDropdown">
                    <ul id="notificationList">
                        </ul>
                </div>
            </div>
        </nav>
    </header>

    <h1 id="history">Volunteer History</h1>

    <table id="volunteerTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Event</th>
                <th>Hours</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- DATA ---
            const volunteerHistory = [
                { date: '2024-01-15', event: 'Beach Cleanup', hours: 5, description: 'Collected trash and debris' },
                { date: '2024-03-22', event: 'Food Drive', hours: 3, description: 'Packed and distributed food' },
                { date: '2024-05-10', event: 'Tree Planting', hours: 4, description: 'Planted trees in the local park' },
                { date: '2025-06-01', event: 'Animal Shelter Support', hours: 6, description: 'Assisted with animal care and cleaning' }
            ];
            
            // Simulates fetching notifications from a server
            let notifications = [
                "New event added: Park Cleanup on Aug 5th",
                "Your hours for 'Animal Shelter Support' were approved.",
                "Reminder: Community Garden setup is this Saturday."
            ];

            // --- DOM ELEMENTS ---
            const tableBody = document.querySelector('#volunteerTable tbody');
            const notificationIcon = document.getElementById('notificationIcon');
            const dropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');

            // --- VOLUNTEER HISTORY MODULE ---
            /**
             * Renders the volunteer history table from the data array.
             */
            function renderVolunteerHistory() {
                // Clear existing table rows to prevent duplication
                tableBody.innerHTML = '';
                
                volunteerHistory.forEach(record => {
                    const row = tableBody.insertRow(); 
                    row.innerHTML = `
                        <td>${record.date}</td>
                        <td>${record.event}</td>
                        <td>${record.hours}</td>
                        <td>${record.description}</td>
                    `;
                });
            }

            // --- NOTIFICATION MODULE ---
            /**
             * Renders notifications in the dropdown and updates the badge.
             */
            function renderNotifications() {
                // Clear any old notifications
                notificationList.innerHTML = '';

                if (notifications.length > 0) {
                    // Update and show the badge
                    notificationBadge.textContent = notifications.length;
                    notificationBadge.classList.add('visible');

                    // Create list items for each notification
                    notifications.forEach(note => {
                        const li = document.createElement('li');
                        li.textContent = note;
                        notificationList.appendChild(li);
                    });
                } else {
                    // Hide badge and show a "no new notifications" message
                    notificationBadge.classList.remove('visible');
                    notificationList.innerHTML = '<li class="no-notifications">No new notifications</li>';
                }
            }
            
           
            // Marks notifications as "read" by clearing the array and re-rendering.
             
            function markNotificationsAsRead() {
                notifications = []; // Clear the notifications array
                renderNotifications(); // Re-render to show empty state
            }

            // --- EVENT LISTENERS ---
            // Toggle dropdown and mark notifications as read when opened
            notificationIcon.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevents the document click listener from firing immediately
                const isActive = dropdown.classList.toggle('active');
                
            });

            // Close dropdown if a click happens outside of it
            document.addEventListener('click', (e) => {
                // Check if the click is outside the notification area and the dropdown is currently open
                if (dropdown.classList.contains('active') && !notificationIcon.contains(e.target) && !dropdown.contains(e.target)) {
                    // Mark as read only if there are notifications to clear
                    if (notifications.length > 0) {
                        markNotificationsAsRead();
                    }
                    dropdown.classList.remove('active');
                }
            });

            // --- INITIAL PAGE LOAD ---
            renderVolunteerHistory();
            renderNotifications();
        });
    </script>
</body>
</html>