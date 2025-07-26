<?php
// Database connection
$host = 'localhost';
$db = 'moodledatabase';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$message = "";
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $requirements = $_POST['requirements'] ?? '';
        $salary = $_POST['salary'] ?? 0;
        $currency = $_POST['currency'] ?? 'LE';
        $location_country = $_POST['location_country'] ?? '';
        $location_city = $_POST['location_city'] ?? '';
        $job_type = $_POST['job_type'] ?? '';
        $posted_by_user_id = $_POST['posted_by_user_id'] ?? 1;
        $employer_id = $_POST['employer_id'] ?? 1;
        $expiry_date = $_POST['expiry_date'] ?? date('Y-m-d', strtotime('+30 days'));
        $status = 'active';
        $created_at = $updated_at = date('Y-m-d H:i:s');

        $tor_file = '';
        if (isset($_FILES['tor_file']) && $_FILES['tor_file']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $tor_file = $target_dir . basename($_FILES['tor_file']['name']);
            move_uploaded_file($_FILES['tor_file']['tmp_name'], $tor_file);
        }

        $stmt = $pdo->prepare("INSERT INTO lmis3_jobs (
            title, description, requirements, salary, currency, location_country,
            location_city, job_type, tor_file, posted_by_user_id, employer_id,
            expiry_date, status, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([$title, $description, $requirements, $salary, $currency,
            $location_country, $location_city, $job_type, $tor_file, $posted_by_user_id,
            $employer_id, $expiry_date, $status, $created_at, $updated_at]);

        $message = "Job successfully posted.";
    }

} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Job</title>
</head>
<body>
    <h1>Add New Job</h1>
    <p><?= htmlspecialchars($message) ?></p>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Description:<br><textarea name="description" required></textarea></label><br>
        <label>Requirements:<br><textarea name="requirements"></textarea></label><br>
        <label>Salary: <input type="number" name="salary" step="0.01" required></label>
        <select name="currency">
            <option value="LE">LE</option>
            <option value="USD">USD</option>
            <option value="EURO">EURO</option>
        </select><br>
        <label>Location Country: <input type="text" name="location_country" required></label><br>
        <label>Location City: <input type="text" name="location_city"></label><br>
        <label>Job Type: <input type="text" name="job_type"></label><br>
        <label>TOR File: <input type="file" name="tor_file" accept=".pdf,.doc,.docx"></label><br>
        <label>Posted By User ID: <input type="number" name="posted_by_user_id" required></label><br>
        <label>Employer ID: <input type="number" name="employer_id" required></label><br>
        <label>Expiry Date: <input type="date" name="expiry_date"></label><br>
        <button type="submit">Submit Job</button>
    </form>
</body>
</html>
