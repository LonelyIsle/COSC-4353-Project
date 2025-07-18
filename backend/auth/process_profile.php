<?php
session_start();

$errors = [];

function sanitize($key){
    return htmlspecialchars(trim($_POST[$key] ?? ''));
}

$fullName = sanitize('full-name');
$address1 = sanitize('Address1');
$city = sanitize('City');
$state = sanitize('State');
$zipcode = sanitize('zipcode');
$preferences = sanitize('Preferences');
$skills = $_POST['skills'] ?? [];
$dates = $_POST['dates'] ?? [];

if (empty($_POST['full-name'])){
    $errors[] = 'Full name is required.';
}
if (empty($_POST['Address1'])){
    $errors[] = 'Address 1 is required.';
}
if (empty($_POST['City'])){
    $errors[] = 'City is required.';
}
if (empty($_POST['State'])){
    $errors[] = 'State is required.';
}

if (!preg_match("/^\d{5}$/", $_POST['zipcode'] ?? "")){
    $errors[] = 'Zip code must be a 5-digit number.';
}

if (empty($_POST['skills']) || !is_array($_POST['skills'])){
    $errors[] = 'At least one skill is required.';
}

if (!empty($_POST['dates']) && is_array($_POST['dates'])){
    foreach ($_POST['dates'] as $date){
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)){
            $errors[] = 'Invalid date format. Use YYYY-MM-DD.';
        }
    }
}

if ($errors){
    foreach($errors as $error){
        echo "<p style='color: red;'>$error</p>";
    }
}else{
    $_SESSION['profile'] = [
        'fullName' => $fullName,
        'address1' => $address1,
        'city' => $city,
        'state' => $state,
        'zipcode' => $zipcode,
        'preferences' => $preferences,
        'skills' => $skills,
        'dates' => $dates
    ];
    echo "<p style='color: green;'>Profile updated successfully!</p>";
}