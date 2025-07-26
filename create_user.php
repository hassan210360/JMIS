<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connection.php';

$username = 'admin';          // Change to your desired username
$password_plain = 'secret123'; // Change to your desired password

try {
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

    $sqlCheck = "SELECT user_id FROM lmis3_users_table WHERE username = ?";
    echo "Debug SQL Check: $sqlCheck<br>";
    $stmt = $conn->prepare($sqlCheck);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception("User '$username' already exists.");
    }

    $sqlInsert = "INSERT INTO lmis3_users_table (username, password_hash) VALUES (?, ?)";
    echo "Debug SQL Insert: $sqlInsert<br>";
    $insert = $conn->prepare($sqlInsert);
    if (!$insert) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $insert->bind_param("ss", $username, $password_hash);

    if ($insert->execute()) {
        echo "User '$username' created successfully.";
    } else {
        throw new Exception("Execute failed: " . $insert->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
