<?php
session_start();
include '../components/navbar.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/css/global.css" />
  <style>
    .dashboard-container {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff8e1;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .tab-buttons {
      display: flex;
      justify-content: space-around;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .tab-buttons button {
      padding: 10px 20px;
      border-radius: 5px;
      background-color: #2e7d32;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .tab-buttons button:hover {
      background-color: #388e3c;
    }

    .tab-content {
      display: none;
      justify-content: center;
      align-items: flex-start;
      width: 100%;
    }

    .tab-content.active {
      display: flex;
    }
  </style>
  <script>
    function showTab(id) {
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => tab.classList.remove('active'));
      document.getElementById(id).classList.add('active');
    }

    window.onload = function() {
      showTab('create-event');
    };
  </script>
</head>
<body>
  <div class="dashboard-container">
    <h2>Admin Dashboard</h2>

    <div class="tab-buttons">
      <button onclick="showTab('create-event')">Create Event</button>
      <button onclick="showTab('make-admin')">Make Admin</button>
      <button onclick="showTab('view-performance')">View Performance</button>
      <button onclick="showTab('event-timeline')">Event Timeline</button>
      <button onclick="showTab('volunteer-match')">Volunteer Match</button>
    </div>

    <div id="create-event" class="tab-content">
      <?php include __DIR__ . '/../backend/script/create_event.php'; ?>
    </div>

    <div id="make-admin" class="tab-content">
      <?php include __DIR__ . '/../backend/script/make_admin.php'; ?>
    </div>

    <div id="view-performance" class="tab-content">
      <?php include __DIR__ . '/../backend/script/view_performance.php'; ?>
    </div>

    <div id="event-timeline" class="tab-content">
      <?php include __DIR__ . '/../backend/script/event_timeline.php'; ?>
    </div>

    <div id="volunteer-match" class="tab-content">
      <?php include __DIR__ . '/../backend/script/volunteer-match.php'; ?>
    </div>
  </div>
</body>
</html>

<script>
  function showTab(id) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    const selected = document.getElementById(id);
    if (selected) selected.classList.add('active');
  }

  window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'create-event';
    showTab(tab);
  };
</script>
