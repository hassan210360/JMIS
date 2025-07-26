<?php
session_start();
require_once 'db_connection.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Get form data
$username = trim($_POST['username']);
$password = $_POST['password'];

// Validate inputs
if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Username and password are required";
    header("Location: login.php");
    exit();
}

try {
    // Prepare SQL to fetch user with profile data
    $stmt = $conn->prepare("
        SELECT u.*, p.first_name, p.last_name 
        FROM lmis3_users_table u
        LEFT JOIN lmis3_user_profiles_table p ON u.user_id = p.user_id
        WHERE u.username = ? OR u.email = ?
    ");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE lmis3_users_table SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->bind_param("i", $user['user_id']);
            $updateStmt->execute();
            
            // Redirect based on user type
            switch ($user['user_type']) {
                case 'admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'employer':
                    header("Location: employer_dashboard.php");
                    break;
                case 'jobseeker':
                    header("Location: jobseeker_dashboard.php");
                    break;
                default:
                    header("Location: dashboard.php");
            }
            exit();
        }
    }
    
    // If we get here, authentication failed
    $_SESSION['error'] = "Invalid username or password";
    header("Location: login.php");
    exit();
    
} catch (Exception $e) {
    error_log("Authentication error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred during login. Please try again.";
    header("Location: login.php");
    exit();
}