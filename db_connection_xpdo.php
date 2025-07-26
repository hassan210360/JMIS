<?php
// Turn on error reporting (for development only)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db   = 'moodledatabase';
$user = 'admin'; // replace with your DB user
$pass = 'MX0WgnmM';     // your MySQL password
$charset = 'utf8'; // safer for older MySQL versions

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>


