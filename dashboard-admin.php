<?php
include 'header.php';
include 'db_connection.php';

// Basic stats
$job_count = $conn->query("SELECT COUNT(*) FROM lmis3_jobs")->fetch_row()[0];
$app_count = $conn->query("SELECT COUNT(*) FROM lmis3_job_applications")->fetch_row()[0];
$user_count = $conn->query("SELECT COUNT(*) FROM lmis3_users_table")->fetch_row()[0];
$guest_app_count = $conn->query("SELECT COUNT(*) FROM lmis3_job_applications WHERE user_type = 'guest'")->fetch_row()[0];
?>

<div class="container mt-5">
  <h2 class="mb-4">📊 Admin Dashboard</h2>
  <div class="row g-4 text-center">
    <div class="col-md-3"><div class="p-3 border bg-light rounded shadow">🗂 <br><strong><?= $job_count ?></strong><br>Total Jobs</div></div>
    <div class="col-md-3"><div class="p-3 border bg-light rounded shadow">📄 <br><strong><?= $app_count ?></strong><br>Applications</div></div>
    <div class="col-md-3"><div class="p-3 border bg-light rounded shadow">👤 <br><strong><?= $user_count ?></strong><br>Users</div></div>
    <div class="col-md-3"><div class="p-3 border bg-light rounded shadow">🔍 <br><strong><?= $guest_app_count ?></strong><br>Guest Apps</div></div>
  </div>
</div>

<?php include 'footer.php'; ?>
