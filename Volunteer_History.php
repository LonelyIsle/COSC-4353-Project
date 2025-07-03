<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        /* Modern Green Navbar */
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
        }

        .dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 2.8rem;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            width: 250px;
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
        }

        .dropdown li:last-child {
            border-bottom: none;
        }

        h1 {
            text-align: center;
            margin-top: 2rem;
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
                <a href="#home">Home</a>
                <a href="#history">Volunteer History</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </div>
            <div class="nav-right">
                <a id="notificationIcon">🔔<span class="badge">3</span></a>
                <div class="dropdown" id="notificationDropdown">
                    <ul>
                        <li>New event added: Park Cleanup</li>
                        <li>Your hours were updated</li>
                        <li>Reminder: Food Drive tomorrow</li>
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
        const volunteerHistory = [
            { date: '2024-01-15', event: 'Beach Cleanup', hours: 5, description: 'Collected trash and debris' },
            { date: '2024-03-22', event: 'Food Drive', hours: 3, description: 'Packed and distributed food' },
            { date: '2024-05-10', event: 'Tree Planting', hours: 4, description: 'Planted trees in the park' }
        ];

        const tableBody = document.querySelector('#volunteerTable tbody');

        volunteerHistory.forEach(record => {
            const row = document.createElement('tr');

            const dateCell = document.createElement('td');
            dateCell.textContent = record.date;

            const eventCell = document.createElement('td');
            eventCell.textContent = record.event;

            const hoursCell = document.createElement('td');
            hoursCell.textContent = record.hours;

            const descCell = document.createElement('td');
            descCell.textContent = record.description;

            row.appendChild(dateCell);
            row.appendChild(eventCell);
            row.appendChild(hoursCell);
            row.appendChild(descCell);

            tableBody.appendChild(row);
        });

        // Dropdown toggle logic
        const notificationIcon = document.getElementById('notificationIcon');
        const dropdown = document.getElementById('notificationDropdown');

        notificationIcon.addEventListener('click', () => {
            dropdown.classList.toggle('active');
        });

        // Optional: close dropdown if click happens outside
        document.addEventListener('click', (e) => {
            if (!notificationIcon.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>
</body>
</html>
