<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("User ID is required.");
}

$user_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT username, email, role FROM lmis3_users_table WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $role = $_POST["role"];
    $new_password = $_POST["password"];

    if ($username && $email && $role) {
        if (!empty($new_password)) {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE lmis3_users_table SET username = ?, email = ?, role = ?, password_hash = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $username, $email, $role, $password_hash, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE lmis3_users_table SET username = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $username, $email, $role, $user_id);
        }

        if ($stmt->execute()) {
            // Audit log
            $log = $conn->prepare("INSERT INTO lmis3_activity_log (username, activity_type) VALUES (?, 'Updated User')");
            $log->bind_param("s", $_SESSION['username']);
            $log->execute();

            header("Location: manage_users.php?updated=1");
            exit;
        } else {
            $error = "Update failed.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h2>Edit User</h2>

    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="employer" <?= $user['role'] === 'employer' ? 'selected' : '' ?>>Employer</option>
                <option value="jobseeker" <?= $user['role'] === 'jobseeker' ? 'selected' : '' ?>>Jobseeker</option>
            </select>
        </div>

        <div class="mb-3">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
