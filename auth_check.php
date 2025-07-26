<?php
// REMOVE session_start() from here if it exists
// Keep only the authentication check:
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}