<?php if (session_status() == PHP_SESSION_NONE) {session_start();}?>

<nav class="navbar">
  <a href="/index.php" class="navbar-logo">
    <img src="images/SecondChance.png" alt="Second Chance Shelter Logo" style="height:140px;">
  </a>
  <div class="navbar-nav">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="pages/profile.php" class="nav-btn">Profile</a>
      <a href="pages/logout.php" class="nav-btn">Logout</a>
    <?php else: ?>
      <a href="pages/Login.php" class="nav-btn">Login</a>
      <a href="pages/Register.php" class="nav-btn">Register</a>
    <?php endif; ?>
  </div>
</nav>