<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

$counts = [
    'employers' => 0,
    'jobseekers' => 0,
    'jobs' => 0,
    'users' => 0,
    'applications' => 0
];

$counts['employers'] = $conn->query("SELECT COUNT(*) FROM lmis3_employers_table")->fetch_row()[0];
$counts['jobseekers'] = $conn->query("SELECT COUNT(*) FROM lmis3_jobseeker_table")->fetch_row()[0];
$counts['jobs'] = $conn->query("SELECT COUNT(*) FROM lmis3_jobs_table")->fetch_row()[0];
$counts['users'] = $conn->query("SELECT COUNT(*) FROM lmis3_users_table")->fetch_row()[0];
$counts['applications'] = $conn->query("SELECT COUNT(*) FROM lmis3_job_applications")->fetch_row()[0];

$recent_users = $conn->query("SELECT username, email, role, last_login FROM lmis3_users_table ORDER BY last_login DESC LIMIT 10");
$recent_activities = $conn->query("SELECT activity_type, username, timestamp FROM lmis3_activity_log ORDER BY timestamp DESC LIMIT 10");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - LMIS</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="main_dashboard.php">Main Dashboard</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="logout.php">Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
  <h2 class="mb-4">Admin Overview</h2>

  <div class="row g-4">
    <?php foreach ($counts as $label => $value): ?>
    <div class="col-md-3">
      <div class="card text-white bg-secondary">
        <div class="card-body">
          <h5 class="card-title text-capitalize"><?= $label ?></h5>
          <p class="card-text display-6"><?= $value ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="row mt-5">
    <div class="col-md-6">
      <h5>Recent Users</h5>
      <table class="table table-bordered">
        <thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Last Login</th></tr></thead>
        <tbody>
          <?php while($u = $recent_users->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['last_login']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="col-md-6">
      <h5>Recent Activity Logs</h5>
      <ul class="list-group">
        <?php while($act = $recent_activities->fetch_assoc()): ?>
        <li class="list-group-item">
          <strong><?= htmlspecialchars($act['username']) ?></strong> - <?= htmlspecialchars($act['activity_type']) ?><br>
          <small><?= htmlspecialchars($act['timestamp']) ?></small>
        </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</div>

<footer class="bg-light text-center py-3 mt-5">
  <div class="container">
    <small>© <?= date('Y') ?> LMIS System | Admin Panel</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
