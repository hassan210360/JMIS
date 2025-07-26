<?php
// Show all PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'header.php';
include 'db_connection.php';

// ✅ Session validation
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'jobseeker') {
   // echo "<div class='alert alert-danger'>⛔ Access denied. Only logged-in jobseekers can apply.</div>";
//    include 'footer.php';
  //  exit;
}

$jobseeker_id = $_SESSION['jobseeker_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($job_id <= 0) {
    echo "<p style='color:red;'>❌ No job ID provided.</p>";
    include 'footer.php';
    exit;
}

// ✅ Fetch job
$stmt = $conn->prepare("SELECT * FROM lmis3_jobs WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    echo "<p style='color:red;'>⚠️ Job not found (job_id = $job_id).</p>";
    include 'footer.php';
    exit;
}

// ✅ Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $cover_letter = $_POST['cover_letter'] ?? '';
    $cv_file = null;

    // ✅ Validate CV upload
    if (!empty($_FILES['cv_file']['name'])) {
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $file_type = $_FILES['cv_file']['type'];
        $file_size = $_FILES['cv_file']['size'];

        if (!in_array($file_type, $allowed_types)) {
            echo "<div class='alert alert-danger'>❌ Invalid file type. Only PDF, DOC, or DOCX allowed.</div>";
            include 'footer.php';
            exit;
        }

        if ($file_size > 2 * 1024 * 1024) {
            echo "<div class='alert alert-danger'>❌ File too large. Max size: 2MB.</div>";
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

    // ✅ Insert into DB
    $insert = $conn->prepare("
        INSERT INTO lmis3_job_applications (job_id, jobseeker_id, full_name, email, cover_letter, cv_file, applied_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $insert->bind_param("iissss", $job_id, $jobseeker_id, $name, $email, $cover_letter, $cv_file);

    if ($insert->execute()) {
        echo "<div class='container mt-5'><div class='alert alert-success'>✅ Application submitted successfully!</div>";
        echo "<a href='my_applications.php' class='btn btn-success mt-2'>📄 View My Applications</a>";
        echo " <a href='jobs_list.php' class='btn btn-secondary mt-2'>← Back to Job List</a></div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Error submitting application: " . $conn->error . "</div>";
    }

    include 'footer.php';
    exit;
}
?>

<!-- ✅ Application form -->
<div class="container mt-5" style="max-width: 700px;">
    <h2 class="mb-4">📄 Apply for: <?= htmlspecialchars($job['title']) ?></h2>

    <p><strong>Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($job['location_city']) ?>, <?= htmlspecialchars($job['location_country']) ?></p>
    <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?> <?= htmlspecialchars($job['currency']) ?></p>
    <p><strong>Expires:</strong> <?= htmlspecialchars($job['expiry_date']) ?></p>
    <hr>

    <form method="post" enctype="multipart/form-data" class="border p-4 bg-light rounded">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cover Letter</label>
            <textarea name="cover_letter" rows="6" class="form-control" placeholder="Why are you applying for this job?"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload CV (PDF, DOC, DOCX)</label>
            <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx" required>
        </div>

        <button type="submit" class="btn btn-primary">📨 Submit Application</button>
    </form>
</div>

<?php include 'footer.php'; ?>
