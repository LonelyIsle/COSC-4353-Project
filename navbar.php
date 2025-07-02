<?php if (session_status() == PHP_SESSION_NONE) {session_start();}?>

<nav class="navbar" style="display: flex; justify-content: space-between; align-items: center;">
  <a href="index.php" class="navbar-logo">
    <img src="svg/logo.svg" alt="Logo" style="height:40px;">
  </a>
  <div class="navbar-nav" style="display: flex; gap: 10px;">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="profile.php" class="nav-btn">Profile</a>
      <a href="logout.php" class="nav-btn">Logout</a>
    <?php else: ?>
      <a href="Login.php" class="nav-btn">Login</a>
      <a href="Register.php" class="nav-btn">Register</a>
    <?php endif; ?>
  </div>
</nav>

<style>
  .nav-btn {
    padding: 8px 16px;
    background-color: #ff6f61;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s ease;
  }

  .nav-btn:hover {
    background-color: #ff8a75;
  }
</style>