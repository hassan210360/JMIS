<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_users.php?error=invalid");
    exit;
}

$user_id = intval($_GET['id']);

// Check user exists
$check_stmt = $conn->prepare("SELECT username FROM lmis3_users_table WHERE user_id = ?");
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_users.php?error=notfound");
    exit;
}

$username = $result->fetch_assoc()['username'];

// Step 1: Delete from user_profiles (child)
$del_profile_stmt = $conn->prepare("DELETE FROM lmis3_user_profiles_table WHERE user_id = ?");
$del_profile_stmt->bind_param("i", $user_id);
$del_profile_stmt->execute();

// Step 2: Delete from users table (parent)
$del_stmt = $conn->prepare("DELETE FROM lmis3_users_table WHERE user_id = ?");
$del_stmt->bind_param("i", $user_id);

if ($del_stmt->execute()) {
    // Log action
    $activity_stmt = $conn->prepare("INSERT INTO lmis3_activity_log (username, activity_type, timestamp) VALUES (?, 'delete_user', NOW())");
    $activity_stmt->bind_param("s", $_SESSION['username']);
    $activity_stmt->execute();

    header("Location: manage_users.php?deleted=1");
    exit;
} else {
    echo "❌ Error deleting user: " . $conn->error;
}
?>
