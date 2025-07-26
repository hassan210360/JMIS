<?php
// File: export_users.php

require_once 'db_connection.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=users_export_' . date('Ymd') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Username', 'Email', 'Role', 'Last Login']);

$sql = "SELECT username, email, role, last_login FROM lmis3_users_table ORDER BY last_login DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['username'], $row['email'], $row['role'], $row['last_login']]);
}

fclose($output);
exit();
