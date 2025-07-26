<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'jobseeker') {
    header("Location: unauthorized.php");
    exit;
}

include 'header.php';
include 'db_connection.php';

$jobseeker_id = $_SESSION['user_id'];

// Fetch application count
$app_count_stmt = $conn->prepare("SELECT COUNT(*) FROM lmis3_job_applications WHERE jobseeker_id = ?");
$app_count_stmt->bind_param("i", $jobseeker_id);
$app_count_stmt->execute();
$app_count = $app_count_stmt->get_result()->fetch_row()[0];
?>

<div class="container mt-4">
    <h3>👋 Welcome, <?= $_SESSION['name'] ?? 'Jobseeker' ?>!</h3>

    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="p-3 bg-light border rounded shadow-sm text-center">
                📄 <br><strong><?= $app_count ?></strong><br>My Applications
            </div>
        </div>

        <div class="col-md-4">
            <a href="jobs_list.php" class="text-decoration-none">
                <div class="p-3 bg-light border rounded shadow-sm text-center">
                    🧭 <br><strong>Find Jobs</strong><br>Browse available jobs
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="edit_profile.php" class="text-decoration-none">
                <div class="p-3 bg-light border rounded shadow-sm text-center">
                    ⚙️ <br><strong>Edit Profile</strong><br>Update info & CV
                </div>
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
