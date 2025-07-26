<?php
include 'db_connection.php';
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=applications_export.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["ID", "Job Title", "Name", "Email", "User Type", "Applied At"]);

$sql = "SELECT a.id, j.title AS job_title, a.full_name, a.email, a.user_type, a.applied_at
        FROM lmis3_job_applications a
        JOIN lmis3_jobs j ON a.job_id = j.job_id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row["id"],
        $row["job_title"],
        $row["full_name"],
        $row["email"],
        $row["user_type"],
        $row["applied_at"]
    ]);
}
fclose($output);
exit;
?>
