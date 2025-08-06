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
      max-width: 900px;
      margin: 40px auto;
      background: #e1f5fe;
      padding: 30px;
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
      background: #0288d1;
      color: white;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .tab-buttons button:hover {
      background: #0277bd;
    }
    .tab-content {
      display: none;
      margin-top: 20px;
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

</body>
</html>
