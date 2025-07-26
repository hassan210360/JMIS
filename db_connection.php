<?php
$host = 'localhost';
$db   = 'moodledatabase';
$user = 'admin';
$pass = 'MX0WgnmM';
$charset = 'utf8'; // ← changed from utf8mb4 to utf8

// Create MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

//----------------------
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
    exit;
}


//-----------------------





// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8");   // utf8mb4
?>
