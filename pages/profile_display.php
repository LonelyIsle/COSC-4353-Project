<?php
require_once __DIR__ . '/../backend/db.php';

$userId = $_SESSION['user_id'] ?? null;

$profile = [
    'full_name' => 'Unknown',
    'address' => 'Unknown',
    'address2' => 'Unknown',
    'city' => 'Unknown',
    'state' => 'Unknown',
    'zipcode' => 'Unknown',
    'skills' => 'Unknown',
    'preferences' => 'Unknown',
    'availability' => 'Unknown'
];
$hasProfile = false;

if ($userId) {
   $stmt = $pdo->prepare("SELECT * FROM UserProfile WHERE user_id = ?");
$stmt->execute([$userId]);
$profileData = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profileData) {
    $profile = $profileData;
    $hasProfile = true;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    

    <div class="centered-page">
        <div class="scrollable-container centered-container" style="padding-top: 4rem;">
            <div class="event-container">
                <h2 style="text-align: left;">Profile Information</h2>
                <div class="form-group">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($profile['full_name'] ?? 'Not provided'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($profile['address'] ?? 'Not provided'); ?></p>
                    <p><strong>Address 2:</strong> <?php echo htmlspecialchars($profile['address2'] ?? 'Not provided'); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($profile['city'] ?? 'Not provided'); ?></p>
                    <p><strong>State:</strong> <?php echo htmlspecialchars($profile['state'] ?? 'Not provided'); ?></p>
                    <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($profile['zipcode'] ?? 'Not provided'); ?></p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($profile['skills'] ?? 'Not provided'); ?></p>
                    <p><strong>Preferences:</strong> <?php echo htmlspecialchars($profile['preferences'] ?? 'Not provided'); ?></p>
                    <p><strong>Availability:</strong><br>
                    <?php
                        $availability = json_decode($profile['availability'] ?? '[]', true);
                        if (!empty($availability)) {
                            echo '<ul>';
                            foreach ($availability as $date) {
                                echo '<li>' . htmlspecialchars($date) . '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo 'Not provided';
                        }
                    ?>
                    </p>
                    <a href="profile.php" class="button-link" style="margin-left: 0;">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
