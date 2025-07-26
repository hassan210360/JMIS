<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $email = $_POST['email'];
    $tmp = $_FILES['cv_file']['tmp_name'];
    $filename = basename($_FILES['cv_file']['name']);
    $cv_dir = 'uploads/';
    if (!is_dir($cv_dir)) mkdir($cv_dir);
    $target = $cv_dir . $filename;
    move_uploaded_file($tmp, $target);

    $conn->query("INSERT IGNORE INTO lmis3_users_table (email, user_type) VALUES ('$email', 'jobseeker')");
    $user_id = $conn->insert_id ?: $conn->query("SELECT user_id FROM lmis3_users_table WHERE email = '$email'")->fetch_assoc()['user_id'];
    $conn->query("INSERT INTO lmis3_user_cvs_table (user_id, file_name) VALUES ($user_id, '$filename')");

    echo "<div class='alert alert-success'>CV uploaded and user profile created (ID: $user_id)</div>";
}
?>
<form method="POST" enctype="multipart/form-data" class="container mt-5">
  <h4>Upload CV (PDF/DOCX)</h4>
  <input class="form-control" type="email" name="email" placeholder="Your Email" required>
  <input class="form-control mt-2" type="file" name="cv_file" required>
  <button class="btn btn-primary mt-2">Upload</button>
</form>
