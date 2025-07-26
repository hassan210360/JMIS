<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'auth_check.php';
require_once 'db_connection.php';

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$target_user_id = intval($_GET['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $national_id = $_POST['national_id'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $education_level = $_POST['education_level'];
    $years_experience = intval($_POST['years_experience']);
    $skills = $_POST['skills'];

    $check = $conn->prepare("SELECT * FROM lmis3_user_profiles_table WHERE user_id = ?");
    $check->bind_param("i", $target_user_id);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();

    if ($exists) {
        $stmt = $conn->prepare("
            UPDATE lmis3_user_profiles_table 
            SET first_name=?, last_name=?, phone=?, national_id=?, date_of_birth=?, gender=?, 
                education_level=?, years_experience=?, skills=?
            WHERE user_id = ?
        ");
        $stmt->bind_param("sssssssisi", $first_name, $last_name, $phone, $national_id, $date_of_birth, $gender,
                          $education_level, $years_experience, $skills, $target_user_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            INSERT INTO lmis3_user_profiles_table 
            (user_id, first_name, last_name, phone, national_id, date_of_birth, gender, education_level, years_experience, skills)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssssis", $target_user_id, $first_name, $last_name, $phone, $national_id, $date_of_birth, $gender,
                          $education_level, $years_experience, $skills);
        $stmt->execute();
    }

    echo "<script>alert('Profile updated successfully.'); window.location='admin_dashboard.php';</script>";
    exit;
}

// Load profile
$stmt = $conn->prepare("SELECT * FROM lmis3_user_profiles_table WHERE user_id = ?");
$stmt->bind_param("i", $target_user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User Profile (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="container mt-4">
    <h3>Edit Jobseeker Profile (User ID: <?= $target_user_id ?>)</h3>
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-6"><label>First Name</label><input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>"></div>
            <div class="col-md-6"><label>Last Name</label><input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"></div>
            <div class="col-md-6"><label>National ID</label><input type="text" name="national_id" class="form-control" value="<?= htmlspecialchars($profile['national_id'] ?? '') ?>"></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($profile['date_of_birth'] ?? '') ?>"></div>
            <div class="col-md-6"><label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select</option>
                    <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="mb-3"><label>Education Level</label><input type="text" name="education_level" class="form-control" value="<?= htmlspecialchars($profile['education_level'] ?? '') ?>"></div>
        <div class="mb-3"><label>Years of Experience</label><input type="number" name="years_experience" class="form-control" value="<?= htmlspecialchars($profile['years_experience'] ?? 0) ?>"></div>
        <div class="mb-3"><label>Skills</label><textarea name="skills" class="form-control" rows="3"><?= htmlspecialchars($profile['skills'] ?? '') ?></textarea></div>
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
