<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
};
require_once 'db_connection.php';

// session_start();


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}


if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    echo "Session expired. Redirecting to login...";
    sleep(2);
    header("Location: login.php");
    exit;
}


$search = $_GET['search'] ?? '';
$users = [];

if ($search !== '') {
    $stmt = $conn->prepare("SELECT user_id, username, email, role FROM lmis3_users_table WHERE username LIKE ? OR email LIKE ? ORDER BY user_id DESC");
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    $users = $conn->query("SELECT user_id, username, email, role FROM lmis3_users_table ORDER BY user_id DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2 class="mb-4">Manage Users</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">✅ User deleted successfully.</div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success">✅ User updated successfully.</div>
    <?php elseif (isset($_GET['added'])): ?>
        <div class="alert alert-success">✅ New user added successfully.</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <form method="get" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Search username or email" value="<?= htmlspecialchars($search ?? '') ?>">
            <button class="btn btn-outline-primary">Search</button>
        </form>
        <a href="add_user.php" class="btn btn-success">➕ Add New User</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['user_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['role'] ?? '') ?></td>
                <td>
                    <a href="edit_user.php?id=<?= urlencode($row['user_id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_user.php?id=<?= urlencode($row['user_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'?>
