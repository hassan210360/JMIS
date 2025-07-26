<?php
session_start();
require_once 'db_connection.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

// Get form data
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$governorate = $_POST['governorate'];
$user_type = 'jobseeker'; // Default to jobseeker for registration form

// Validate inputs
$errors = [];

if (empty($first_name)) {
    $errors[] = "First name is required";
}

if (empty($last_name)) {
    $errors[] = "Last name is required";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}

if (empty($username)) {
    $errors[] = "Username is required";
}

if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match";
}

if (empty($governorate)) {
    $errors[] = "Governorate is required";
}

// Check if username or email already exists
$stmt = $conn->prepare("SELECT user_id FROM lmis3_users_table WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $errors[] = "Username or email already exists";
}

// If there are errors, redirect back to registration form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'username' => $username,
        'governorate' => $governorate
    ];
    header("Location: register.php");
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into database
try {
    $conn->begin_transaction();
    
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO lmis3_users_table (username, password_hash, email, user_type, registration_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $user_type);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    
    // Insert into user profiles table
    $stmt = $conn->prepare("INSERT INTO lmis3_user_profiles_table (user_id, first_name, last_name, governorate) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $first_name, $last_name, $governorate);
    $stmt->execute();
    
    $conn->commit();
    
    // Set success message and redirect
    $_SESSION['success'] = "Registration successful! Please login with your credentials.";
    header("Location: login.php");
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['errors'] = ["Registration failed. Please try again."];
    error_log("Registration error: " . $e->getMessage());
    header("Location: register.php");
    exit();
}