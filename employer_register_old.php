<?php
// Show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_name = $_POST['company_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $contact_person = $_POST['contact_person'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $industry = $_POST['industry'] ?? '';

    // Validation
    if (empty($company_name) || empty($email) || empty($password)) {
        die("Required fields are missing.");
    }

    // Check if email already registered
    $check = $conn->prepare("SELECT employer_id FROM lmis3_employers_table WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("Email already registered.");
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert employer
    $stmt = $conn->prepare("INSERT INTO lmis3_employers_table (company_name, email, password_hash, contact_person, phone, industry, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $company_name, $email, $hashed, $contact_person, $phone, $industry);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Employer registered successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Register as Employer</h3>
    <form method="POST" action="">
        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" name="company_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contact Person</label>
            <input type="text" name="contact_person" class="form-control">
        </div>
        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Industry</label>
            <input type="text" name="industry" class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
</body>
</html>
