<?php
include 'header.php';

$pdo = new PDO("mysql:host=localhost;dbname=moodledatabase", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$job_id = $_GET['id'] ?? null;

if ($job_id) {
    // Delete permanently
    $stmt = $pdo->prepare("DELETE FROM lmis3_jobs WHERE id = ?");
    $stmt->execute([$job_id]);

    echo "<p style='color:green;'>Job #$job_id has been permanently deleted.</p>";
    echo "<p><a href='jobs_list.php'>← Back to Job List</a></p>";
} else {
    echo "<p style='color:red;'>No job ID specified.</p>";
}

include 'footer.php';
