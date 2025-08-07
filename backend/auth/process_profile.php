<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

function clean(string $key): string {
    return trim($_POST[$key] ?? '');
}

$fullName   = clean('full-name');              // required
$address1   = clean('Address1');               // required
$address2   = clean('Address2');               // optional
$city       = clean('City');                   // required
$state      = strtoupper(substr(clean('State'), 0, 2)); // normalize to 2-letter
$zipcode    = clean('zipcode');                // required, 5-digits
$preferences= clean('Preferences');            // optional
$skillsArr  = $_POST['skills'] ?? [];          // required array
$datesArr   = $_POST['dates']  ?? [];          // optional array

$errors = [];

if ($fullName === '')           $errors[] = 'Full name is required.';
if ($address1 === '')           $errors[] = 'Address 1 is required.';
if ($city === '')               $errors[] = 'City is required.';
if ($state === '' || strlen($state) !== 2)
                                $errors[] = 'State must be 2 letters.';
if (!preg_match('/^\d{5}$/', $zipcode))
                                $errors[] = 'Zip code must be 5 digits.';
if (empty($skillsArr) || !is_array($skillsArr))
                                $errors[] = 'At least one skill is required.';

foreach ($datesArr as $d) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
        $errors[] = "Invalid date: $d (use YYYY-MM-DD)";
    }
}

if ($errors) {
    echo "<script>alert('".implode("\\n", $errors)."'); window.history.back();</script>";
    exit;
}

$skillsJSON       = json_encode(array_values($skillsArr), JSON_UNESCAPED_UNICODE);
$availabilityJSON = !empty($datesArr)
    ? json_encode(array_values($datesArr), JSON_UNESCAPED_UNICODE)
    : null;


try {
    $userId = $_SESSION['user_id'];

    $stateName = $state; 
    $stmt = $pdo->prepare("INSERT IGNORE INTO States (state_code, state_name) VALUES (:code, :name)");
    $stmt->execute([
        ':code' => $state,
        ':name' => $stateName
    ]);

    $stmt = $pdo->prepare("SELECT 1 FROM UserProfile WHERE user_id = :uid");
    $stmt->execute([':uid' => $userId]);
    $profileExists = $stmt->fetchColumn();

    if ($profileExists) {
        $sql = "UPDATE UserProfile
                SET full_name   = :full_name,
                    address     = :address,
                    address2    = :address2,
                    city        = :city,
                    state       = :state,
                    zipcode     = :zipcode,
                    skills      = :skills,
                    preferences = :preferences,
                    availability= :availability
                WHERE user_id   = :uid";
    } else {
        $sql = "INSERT INTO UserProfile
                (user_id, full_name, address, address2, city, state, zipcode,
                 skills, preferences, availability)
                VALUES
                (:uid, :full_name, :address, :address2, :city, :state, :zipcode,
                 :skills, :preferences, :availability)";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':uid'          => $userId,
        ':full_name'    => $fullName,
        ':address'      => $address1,
        ':address2'     => $address2,
        ':city'         => $city,
        ':state'        => $state,
        ':zipcode'      => $zipcode,
        ':skills'       => $skillsJSON,
        ':preferences'  => $preferences,
        ':availability' => $availabilityJSON,
    ]);

    
    $_SESSION['profile'] = [
        'user_id'     => $userId,
        'fullName'    => $fullName,
        'address'     => $address1,
        'address2'    => $address2,
        'city'        => $city,
        'state'       => $state,
        'zipcode'     => $zipcode,
        'preferences' => $preferences,
        'skills'      => $skillsArr,
        'dates'       => $datesArr,
    ];

    error_log("Profile saved. User ID: ".$userId);

    echo "<script>alert('Profile successfully updated!');</script>";
    header('Location: /pages/volunteer_dashboard.php?success=1');
    exit;

} catch (PDOException $e) {
    error_log('Profile DB error: '.$e->getMessage());
    echo "<script>alert('Database error: ".addslashes($e->getMessage())."'); window.history.back();</script>";
    exit;
}
?>