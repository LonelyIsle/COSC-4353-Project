
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <a href="/index.html" class="navbar-logo">
    <img src="/images/SecondChance.png" alt="Logo" style="height:140px;">
  </a>
  <div class="navbar-nav">
    <?php if ($current_page !== 'services.php'): ?>
      <a href="/pages/services.php" class="nav-btn">Volunteer Services</a>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if (($_SESSION['role'] ?? '') === 'admin' && $current_page !== 'admin_dashboard.php'): ?>
        <a href="/pages/admin_dashboard.php" class="nav-btn">Admin Dashboard</a>
      <?php elseif (($_SESSION['role'] ?? '') === 'volunteer' && $current_page !== 'volunteer_dashboard.php'): ?>
        <a href="/pages/volunteer_dashboard.php" class="nav-btn">My Dashboard</a>
      <?php elseif (($_SESSION['role'] ?? '') === 'volunteer' && $current_page !== 'Volunteer_History.php'): ?>
        <a href="/pages/Volunteer_History.php" class="nav-btn">Volunteer History</a>
      <?php endif; ?>

      <a href="/backend/auth/Logout_backend.php" class="nav-btn">Logout</a>
    <?php else: ?>
      <a href="/pages/Login.php" class="nav-btn">Login</a>
      <a href="/pages/Register.php" class="nav-btn">Register</a>
    <?php endif; ?>
  </div>
</nav>

