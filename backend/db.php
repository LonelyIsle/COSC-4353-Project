
<?php
// 1) Autoload Composer (one level up from backend/)
require_once __DIR__ . '/../vendor/autoload.php';

// 2) Tell phpdotenv to load .env from your project root
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// 3) Now grab your DB creds from $_ENV
$host    = $_ENV['DB_HOST']    ?? '';
$db      = $_ENV['DB_NAME']    ?? '';
$user    = $_ENV['DB_USER']    ?? '';
$pass    = $_ENV['DB_PASS']    ?? '';
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