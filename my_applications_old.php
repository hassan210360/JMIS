<?php
// ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'auth_check.php';

if ($_SESSION['user_type'] !== 'jobseeker') {
    header("Location: unauthorized.php");
    exit();
}

require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT a.*, j.title AS job_title, j.employment_type, j.governorate, j.closing_date
    FROM lmis3_applications_table a
    JOIN lmis3_jobs_table j ON a.job_id = j.job_id
    WHERE a.jobseeker_id = ?
    ORDER BY a.application_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications - Egypt LMIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include 'jobseeker_sidebar.php'; ?>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">My Job Applications</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($applications)): ?>
                        <div class="list-group">
                            <?php foreach ($applications as $app): ?>
                                <div class="list-group-item mb-2 border-start border-primary border-4">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="mb-1"><?= htmlspecialchars($app['job_title']); ?></h5>
                                            <p class="mb-1 text-muted">
                                                <?= htmlspecialchars($app['governorate']); ?> | <?= ucfirst($app['employment_type']); ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Applied on <?= date('M d, Y', strtotime($app['application_date'])); ?></small><br>
                                            <span class="badge bg-<?php
                                                switch ($app['application_status']) {
                                                    case 'pending': echo 'secondary'; break;
                                                    case 'reviewed': echo 'info'; break;
                                                    case 'accepted': echo 'success'; break;
                                                    case 'rejected': echo 'danger'; break;
                                                    default: echo 'dark';
                                                }
                                            ?>">
                                                <?= ucfirst($app['application_status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="job_details.php?id=<?= $app['job_id']; ?>" class="btn btn-sm btn-outline-primary">View Job</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            You haven't applied to any jobs yet. Start browsing jobs <a href="job_search.php">here</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
