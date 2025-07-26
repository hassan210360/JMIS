<?php
include 'db_connection.php';
session_start();

// Simulated logged-in jobseeker (replace with real session check)
$jobseeker_id = $_SESSION['jobseeker_id'] ?? 1;

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$success = '';
$error = '';

if ($job_id > 0 && $jobseeker_id) {
    // Optional: Check if already applied
    $check = $conn->prepare("SELECT id FROM job_applications WHERE job_id = ? AND jobseeker_id = ?");
    $check->bind_param("ii", $job_id, $jobseeker_id);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        $error = "You have already applied for this job.";
    } else {
        $stmt = $conn->prepare("INSERT INTO job_applications (job_id, jobseeker_id, application_date) VALUES (?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ii", $job_id, $jobseeker_id);
            if ($stmt->execute()) {
                $success = "Application submitted successfully.";
            } else {
                $error = "Error submitting application.";
            }
        } else {
            $error = "Failed to prepare application statement.";
        }
    }
} else {
    $error = "Invalid job or user.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card p-4 shadow">
        <h2>Job Application</h2>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <a href="search_jobs.php" class="btn btn-secondary">Back to Job Listings</a>
    </div>
</div>
</body>
</html>
