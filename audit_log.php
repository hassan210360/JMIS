<?php
require_once 'db_connection.php';

function log_event($username, $activity_type) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO lmis3_activity_log (username, activity_type, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $activity_type);
    $stmt->execute();
}