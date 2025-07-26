<?php
// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'db_connection.php';

// Validate job_id
$job_id = $_GET['job_id'] ?? null;
if (!$job_id || !is_numeric($job_id)) {
    echo "<div class='container mt-4 alert alert-danger'>⛔ Invalid or missing job ID.</div>";
    include 'footer.php';
    exit;
}

// Fetch job details
$job_stmt = $conn->prepare("SELECT title FROM lmis3_jobs WHERE job_id = ?");
$job_stmt->bind_param("i", $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();
$job = $job_result->fetch_assoc();

if (!$job) {
    echo "<div class='container mt-4 alert alert-danger'>⛔ Job not found.</div>";
    include 'footer.php';
    exit;
}

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $cover_letter = trim($_POST['cover_letter']);
    $user_type = $_SESSION['user_type'] ?? 'guest';
    $jobseeker_id = $_SESSION['user_id'] ?? null;

    // Validate and handle file upload
    $cv_file = null;
    if (!empty($_FILES['cv_file']['name'])) {
        $allowed_exts = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_exts)) {
            echo "<div class='container mt-4 alert alert-danger'>⛔ Invalid file type. Only PDF, DOC, DOCX allowed.</div>";
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

    // Save to DB
    $stmt = $conn->prepare("INSERT INTO lmis3_job_applications (job_id, jobseeker_id, full_name, email, cover_letter, cv_file, user_type, applied_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssss", $job_id, $jobseeker_id, $name, $email, $cover_letter, $cv_file, $user_type);
    $stmt->execute();

    // Send confirmation email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subject = "✅ Your Application for '{$job['title']}'";
        $message = "Dear $name,\n\nThank you for applying to '{$job['title']}'.\nWe have received your application.\n\nRegards,\nLMIS Team";
        $headers = "From: LMIS Platform <noreply@lmis.org>";
        @mail($email, $subject, $message, $headers);
    }

    // Redirect to thank you page
    header("Location: application_success.php");
    exit;
}
?>

<!-- Application Form -->
<div class="container mt-5">
    <h3>📄 Apply for: <?= htmlspecialchars($job['title']) ?></h3>

    <form method="post" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Full Name:</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Letter:</label>
            <textarea name="cover_letter" rows="5" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload CV (PDF, DOC, DOCX):</label>
            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx" required>
        </div>

        <button type="submit" class="btn btn-primary">📨 Submit Application</button>
    </form>
</div>

<?php include 'footer.php'; ?>
