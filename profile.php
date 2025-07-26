<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'auth_check.php';
require_once 'db_connection.php';

if ($_SESSION['user_type'] !== 'jobseeker') {
    header("Location: unauthorized.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("
    SELECT u.email, u.username, u.user_type,
           p.first_name, p.last_name, p.phone, p.national_id, p.date_of_birth, 
           p.gender, p.education_level, p.years_experience, p.skills
    FROM lmis3_users_table u
    LEFT JOIN lmis3_user_profiles_table p ON u.user_id = p.user_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<div class='alert alert-danger'>User profile not found.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
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
            <h3>My Profile</h3>
            <table class="table table-bordered mt-3">
                <tr><th>First Name</th><td><?= htmlspecialchars($user['first_name']) ?></td></tr>
                <tr><th>Last Name</th><td><?= htmlspecialchars($user['last_name']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                <tr><th>Username</th><td><?= htmlspecialchars($user['username']) ?></td></tr>
                <tr><th>Phone</th><td><?= htmlspecialchars($user['phone']) ?></td></tr>
                <tr><th>National ID</th><td><?= htmlspecialchars($user['national_id']) ?></td></tr>
                <tr><th>Date of Birth</th><td><?= htmlspecialchars($user['date_of_birth']) ?></td></tr>
                <tr><th>Gender</th><td><?= htmlspecialchars($user['gender']) ?></td></tr>
                <tr><th>Education Level</th><td><?= htmlspecialchars($user['education_level']) ?></td></tr>
                <tr><th>Years of Experience</th><td><?= htmlspecialchars($user['years_experience']) ?></td></tr>
                <tr><th>Skills</th><td><?= htmlspecialchars($user['skills']) ?></td></tr>
            </table>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
