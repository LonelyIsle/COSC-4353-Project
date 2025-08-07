<?php
// DO  NOT TOUCH THIS FILE - HENRY
// Oaquired from apache set env
$host    = getenv('DB_HOST');
$db      = getenv('DB_NAME');
$user    = getenv('DB_USER');
$pass    = getenv('DB_PASS');
$charset = 'utf8mb4';

if (!$host || !$db || !$user || !$pass) {
    echo 'Database configuration is incomplete.';
    exit;
}

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {

    echo 'DB connection failed.';
    exit;
}