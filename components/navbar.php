<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar">
  <a href="/index.html" class="navbar-logo">
    <img src="/images/SecondChance.png" alt="Logo" style="height:140px;">
  </a>
  <div class="navbar-nav">
    <?php if ($current_page !== 'services.php'): ?>
      <a href="/pages/services.php" class="nav-btn">Our Services</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="/pages/profile_display.php" class="nav-btn">Profile</a>
      <a href="/backend/auth/Logout_backend.php" class="nav-btn">Logout</a>
    <?php else: ?>
      <a href="/pages/Login.php" class="nav-btn">Login</a>
      <a href="/pages/Register.php" class="nav-btn">Register</a>
    <?php endif; ?>
  </div>
</nav>
