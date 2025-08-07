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
