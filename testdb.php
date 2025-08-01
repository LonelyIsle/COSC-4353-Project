<?php
echo "__DIR__ = " . __DIR__ . "\n";

require_once __DIR__ . '/backend/db.php';

try {
    $pdo->query('SELECT 1');
    echo " DB is connectable\n";
} catch (PDOException $e) {
    echo "DB connection failed: " . $e->getMessage() . "\n";
}
