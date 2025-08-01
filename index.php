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
    <h1>Second Chance Shelter</h1>

    <p>Welcome to Second Chance Shelter, a safe haven dedicated to providing support and resources for those in need. We are committed to helping individuals and families rebuild their lives with dignity and hope.</p>

    

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Second Chance Shelter. All rights reserved.</p>
        <p>Contact us: info@secondchanceshelter.org</p>
    </footer>
</body>
</html>