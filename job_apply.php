<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'db_connection.php';

// Get job ID from URL
$job_id = $_GET['job_id'] ?? null;
if (!$job_id || !is_numeric($job_id)) {
    echo "<p style='color:red;'>⛔ No valid job ID specified.</p>";
    include 'footer.php';
    exit;
}

// Get job details
$job_stmt = $conn->prepare("SELECT * FROM lmis3_jobs WHERE job_id = ?");
$job_stmt->bind_param("i", $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();
$job = $job_result->fetch_assoc();

if (!$job) {
    echo "<p style='color:red;'>⛔ Job not found.</p>";
    include 'footer.php';
    exit;
}

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $cover_letter = trim($_POST['cover_letter']);
    $user_type = $_SESSION['user_type'] ?? 'guest';
    $jobseeker_id = $_SESSION['user_id'] ?? null;

    // Validate file
    $cv_file = null;
    if (!empty($_FILES['cv_file']['name'])) {
        $allowed_exts = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts)) {
            echo "<p style='color:red;'>⛔ Invalid file type. Only PDF, DOC, DOCX allowed.</p>";
            include 'footer.php';
            exit;
        }

        $upload_dir = "uploads/cvs/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $cv_file = time() . '_' . basename($_FILES['cv_file']['name']);
        move_uploaded_file($_FILES['cv_file']['tmp_name'], $upload_dir . $cv_file);
    }

    // Insert application
    $insert = $conn->prepare("INSERT INTO lmis3_job_applications (job_id, jobseeker_id, full_name, email, cover_letter, cv_file, user_type, applied_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $insert->bind_param("iisssss", $job_id, $jobseeker_id, $name, $email, $cover_letter, $cv_file, $user_type);
    $insert->execute();

    // Send email (optional)
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subject = "Thank you for applying to {$job['title']}";
        $message = "Dear $name,\n\nThank you for applying to the job titled '{$job['title']}'.\nWe will contact you if you're shortlisted.\n\nRegards,\nLMIS Team";
        $headers = "From: no-reply@lmis.org";
        @mail($email, $subject, $message, $headers); // Use @ to avoid warnings if mail() fails
    }

    // Redirect to thank-you screen
    header("Location: application_success.php");
    exit;
}
?>

<div class="container mt-4">
    <h3>📄 Apply for: <?= htmlspecialchars($job['title']) ?></h3>

    <form method="post" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Full Name:</label>
            <input type="text" name="full_name" required class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Email Address:</label>
            <input type="email" name="email" required class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Letter:</label>
            <textarea name="cover_letter" rows="5" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload CV (PDF, DOC, DOCX):</label>
            <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">📨 Submit Application</button>
    </form>
</div>
 


<?php include 'footer.php'; ?>






