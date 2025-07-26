<?php
require_once 'db_connection.php'; // make sure this file connects $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($company_name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO employers (company_name, email, password_hash) VALUES (?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sss", $company_name, $email, $password_hash);

            if ($stmt->execute()) {
                $message = "✅ Employer registered successfully.";
                $success = true;
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "❌ Prepare failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Employer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2>Register New Employer</h2>
        <?php if (isset($message)): ?>
            <div class="alert <?= isset($success) ? 'alert-success' : 'alert-danger' ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register Employer</button>
        </form>

        <hr>
        <a href="view_employers.php" class="btn btn-outline-secondary mt-3">View All Employers</a>
    </div>
</body>
</html>
