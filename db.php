<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'moodledatabase';
$db_user = 'admin';
$db_pass = 'MX0WgnmM';
$charset = 'utf8mb4';

// Enable error logging
function write_log($type, $message) {
    $logFile = __DIR__ . '/logs/db_errors.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp][$type] $message" . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}

// PDO setup
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";
    $conn = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    write_log('DB_ERROR', $e->getMessage());

    // Show full error only if local environment
    if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
        die("Database error: " . $e->getMessage());
    } else {
        die("A database error occurred. Please try again later.");
    }
}
?>
