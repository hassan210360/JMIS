<?php include('header.php'); ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// session_start(); // Uncomment if using sessions
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employer Dashboard</title>
    <meta charset="UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="main_dashboard.php">Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="main_dashboard.php">Main Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="employer_dashboard.php">Employer Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="view_employers.php">View Employers</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="employer_register.php">Add Employer</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <h2 class="mb-4">Welcome to the Employer Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <h5 class="card-title">View All Employers</h5>
                    <p class="card-text">Browse and manage all registered employers.</p>
                    <a href="view_employers.php" class="btn btn-primary">Go to Employers List</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <h5 class="card-title">Register New Employer</h5>
                    <p class="card-text">Add a new company to the employer database.</p>
                    <a href="employer_register.php" class="btn btn-success">Add Employer</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <h5 class="card-title">Edit Employer Info</h5>
                    <p class="card-text">Modify existing employer details (start from the list).</p>
                    <a href="view_employers.php" class="btn btn-warning">Edit Employer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 text-end">
        <a href="main_dashboard.php" class="btn btn-outline-secondary">← Back to Main Dashboard</a>
    </div>
</div>

<!-- FOOTER -->
<footer class="bg-light text-center py-3 mt-5">
  <div class="container">
    <small>© <?= date('Y') ?> LMIS System | Employer Module</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
