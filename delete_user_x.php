<?php
session_start();
require_once 'db_connection.php';
require_once 'audit_log.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM lmis3_users_table WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    log_event($_SESSION['username'], "deleted_user_id_$id");
}

header("Location: manage_users.php?deleted=1");
exit;