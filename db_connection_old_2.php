<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'moodledatabase';
$db_user = 'admin';
$db_pass = 'MX0WgnmM';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
