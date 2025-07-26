<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'moodledatabase';
$db_user = 'admin';
$db_pass = 'MX0WgnmM';
$charset = 'utf8mb4';

// DSN for PDO
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Prevents SQL injection via emulation
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
