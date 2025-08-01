<?php
// backend/db.php

require_once __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();  
}

$host    = $_ENV['DB_HOST']    ?? getenv('DB_HOST')    ?? '';
$db      = $_ENV['DB_NAME']    ?? getenv('DB_NAME')    ?? '';
$user    = $_ENV['DB_USER']    ?? getenv('DB_USER')    ?? '';
$pass    = $_ENV['DB_PASS']    ?? getenv('DB_PASS')    ?? '';
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
    echo 'DB connection failed: ' . $e->getMessage();
    exit;
}