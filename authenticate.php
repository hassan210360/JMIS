<?php
session_start();
require_once 'db_connection.php';
require_once 'audit_log.php';
$_SESSION['user_id'] = $row['user_id']; // or whatever your ID field is
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM lmis3_users_table WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        log_event($user['username'], 'login_success');

        $redirect = $_SESSION['redirect_after_login'] ?? 'main_dashboard.php';
        unset($_SESSION['redirect_after_login']);
        header("Location: $redirect");
        exit();
    }
}

log_event($username, 'login_failed');
$_SESSION['error'] = "Invalid credentials.";
header("Location: login.php");
exit();