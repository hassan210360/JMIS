<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db_connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid or missing employer ID.");
}

$employer_id = intval($_GET['id']);
$error = '';
$company_name = '';
$email = '';

// Fetch employer data
$stmt = $conn->prepare("SELECT company_name, email FROM lmis3_employers_table WHERE employer_id = ?");
$stmt->bind_param("i", $employer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Employer not found.");
}

$employer = $result->fetch_assoc();
$company_name = $employer['company_name'];
$email = $employer['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_name = trim($_POST['company_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($company_name === '' || $email === '') {
        $error = "Company name and email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $update_stmt = $conn->prepare("UPDATE lmis3_employers_table SET company_name = ?, email = ? WHERE employer_id = ?");
        $update_stmt->bind_param("ssi", $company_name, $email, $employer_id);
        if ($update_stmt->execute()) {
            header("Location: view_employers.php?updated=1");
            exit;
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Employer</title>
    <meta charset="UTF-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">LMIS Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
      aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
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

<div class="container" style="max-width:600px;">
    <h2>Edit Employer #<?= htmlspecialchars($employer_id) ?></h2>
    
    <!-- Back to Employers List button -->
    <a href="view_employers.php" class="btn btn-outline-secondary mb-3">← Back to Employers List</a>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input
                type="text"
                name="company_name"
                id="company_name"
                class="form-control"
                value="<?= htmlspecialchars($company_name) ?>"
                required
            />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                value="<?= htmlspecialchars($email) ?>"
                required
            />
        </div>

        <button type="submit" class="btn btn-success">Update Employer</button>
        <a href="view_employers.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<!-- FOOTER -->
<footer class="bg-light text-center py-3 mt-5">
  <div class="container">
    <small>© <?= date('Y') ?> LMIS System</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
