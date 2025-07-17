<?php session_start();?>

<?php include 'components/navbar.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Second Chance Shelter</title>
    <link rel="stylesheet" href="/css/global.css">
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
    <h1>Second Chance Shelter</h1>

    <p>Welcome to Second Chance Shelter, a safe haven dedicated to providing support and resources for those in need. We are committed to helping individuals and families rebuild their lives with dignity and hope.</p>

    <section id="services">
        <h2>Our Services</h2>
        <ul>
            <li>Emergency Housing Assistance</li>
            <li>Food and Clothing Distribution</li>
            <li>Counseling and Support Groups</li>
            <li>Job Training and Placement</li>
            <li>Community Outreach Programs</li>
        </ul>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Second Chance Shelter. All rights reserved.</p>
        <p>Contact us: info@secondchanceshelter.org</p>
    </footer>
</body>
</html>