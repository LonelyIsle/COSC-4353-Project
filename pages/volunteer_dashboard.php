<?php

session_start();

if (($_SESSION['role'] ?? '') !== 'volunteer') {
    header('Location: /pages/Login.php');
    exit;
}

include __DIR__ . '/../components/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Volunteer Dashboard</title>
  <link rel="stylesheet" href="/css/global.css" />
  <style>
    .dashboard-container {
      max-width: 750px;
      margin: 40px auto;
      background: #fff8e1;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .tab-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }
    .tab-buttons button {
      flex: 1;
      padding: 10px;
      border: none;
      background: #2e7d32;
      color: white;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .tab-buttons button:hover {
      background: #388e3c;
    }
    .tab-content {
      display: none;
      margin-top: 20px;
      background: #fefae0;
      padding: 18px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    .tab-content.active {
      display: block;
    }
  </style>
  <script>
    function showTab(id) {
      document.querySelectorAll('.tab-content')
        .forEach(tab => tab.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }
    window.onload = () => showTab('edit-profile');
  </script>
</head>
<body>

  <div class="dashboard-container">
    <h2>Volunteer Dashboard</h2>

    <div class="tab-buttons">
      <button onclick="showTab('view-profile')">View Profile</button>
      <button onclick="showTab('my-assignments')">My Assignments</button>
    </div>


    <div id="view-profile" class="tab-content">
      <?php include __DIR__ . '/profile_display.php'; ?>
    </div>

    <div id="my-assignments" class="tab-content">
      <?php include __DIR__ . '/assignments_calendar.php'; ?>
    </div>
  </div>
  <div class="page-notification-container">
        <button id="pageNotificationBtn" title="Notifications">
            🔔
            <span id="notificationBadge" class="badge"></span>
        </button>
        <div id="notificationDropdown" class="dropdown">
            <ul id="notificationList"></ul>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const pageNotificationBtn = document.getElementById('pageNotificationBtn');
  const dropdown = document.getElementById('notificationDropdown');
  const notificationList = document.getElementById('notificationList');
  const notificationBadge = document.getElementById('notificationBadge');

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

  pageNotificationBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('active');
  });

  document.addEventListener('click', (e) => {
    if (
      dropdown.classList.contains('active') &&
      !pageNotificationBtn.contains(e.target) &&
      !dropdown.contains(e.target)
    ) {
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
