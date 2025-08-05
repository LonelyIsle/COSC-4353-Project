<?php session_start();?>

<?php include_once __DIR__ . '/components/navbar.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Second Chance Shelter</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-box">
            <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <div class="homepage-container" style="margin-top: 40px;">
        <img src="images/Volunteer.jpg" alt="Shelter Community" class="homepage-image margin-left" style="margin-top: -500px;">
        <div class="homepage-content" style="margin-top: -500px;">
            <h1>Welcome to Second Chance Shelter</h1>
            <p>We are committed to helping individuals and families rebuild their lives with dignity and hope.</p>
            <a href="pages/register.php" class="btn">Join Us</a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Second Chance Shelter. All rights reserved.</p>
        <p>Contact us: info@secondchanceshelter.org</p>
    </footer>
</body>
</html>