<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'auth_check.php';
require_once 'db_connection.php';

if ($_SESSION['user_type'] !== 'jobseeker') {
    header("Location: unauthorized.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all applications by this user
$stmt = $conn->prepare("
    SELECT a.applied_at, j.title, j.job_id, j.governorate, j.closing_date, j.status
    FROM lmis3_applications_table a
    JOIN lmis3_jobs_table j ON a.job_id = j.job_id
    WHERE a.jobseeker_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Applications | LMIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include 'jobseeker_sidebar.php'; ?>
        </div>
        <div class="col-md-9">
            <h3 class="mb-4">My Job Applications</h3>

            <?php if (!empty($applications)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Governorate</th>
                            <th>Applied At</th>
                            <th>Status</th>
                            <th>Closing Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['title']); ?></td>
                            <td><?php echo htmlspecialchars($app['governorate']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                            <td>
                                <?php echo $app['status'] === 'active' ? '<span class="badge bg-success">Open</span>' : '<span class="badge bg-secondary">Closed</span>'; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($app['closing_date'])); ?></td>
                            <td>
                                <a href="job_details.php?id=<?php echo $app['job_id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">You haven't applied to any jobs yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
