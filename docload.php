<?php
// File: docload.php
session_start();
require_once 'db_connection.php';

function safe_filename($name) {
    return preg_replace("/[^a-zA-Z0-9\\._-]/", "_", strtolower($name));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']) ?: 'Unknown';
    $cv_file = $_FILES['cv_file'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email.");
    }

    $allowed_ext = ['pdf', 'doc', 'docx'];
    $upload_dir = 'uploads/cv/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $ext = strtolower(pathinfo($cv_file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        die("Only PDF, DOC, and DOCX files are allowed.");
    }

    $unique_name = uniqid('cv_') . '_' . safe_filename($cv_file['name']);
    $target_path = $upload_dir . $unique_name;

    if (move_uploaded_file($cv_file['tmp_name'], $target_path)) {
        // Check or create user
        $stmt = $conn->prepare("SELECT user_id FROM lmis3_users_table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $password = password_hash('Default123!', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO lmis3_users_table (email, first_name, password, user_type) VALUES (?, ?, ?, 'jobseeker')");
            $stmt->bind_param("sss", $email, $name, $password);
            $stmt->execute();
            $user_id = $stmt->insert_id;
        } else {
            $user_id = $result->fetch_assoc()['user_id'];
        }

        // Save or update CV path
        $stmt = $conn->prepare("
            INSERT INTO lmis3_user_profiles_table (user_id, full_name, cv_path)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE cv_path = VALUES(cv_path), full_name = VALUES(full_name)
        ");
        $stmt->bind_param("iss", $user_id, $name, $target_path);
        $stmt->execute();

        echo "<div class='alert alert-success m-3'>✅ CV uploaded successfully and profile updated.</div>";
    } else {
        echo "<div class='alert alert-danger m-3'>❌ Failed to upload the file.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload CV</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h2>Upload Your CV</h2>
  <form method="POST" enctype="multipart/form-data" class="border p-4 shadow rounded bg-light">
    <div class="mb-3">
      <label>Email address</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Full Name</label>
      <input type="text" name="name" class="form-control">
    </div>
    <div class="mb-3">
      <label>Upload CV (PDF, DOC, DOCX)</label>
      <input type="file" name="cv_file" class="form-control" accept=\".pdf,.doc,.docx\" required>
    </div>
    <button class="btn btn-primary">Upload CV</button>
  </form>
</body>
</html>
