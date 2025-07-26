<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle);
        $inserted = 0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            list($occupation_name, $isco_code, $esco_code, $description) = $data;

            $stmt = $conn->prepare("
                INSERT INTO lmis3_occupations_table (occupation_name, isco_code, esco_code, description)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE description = VALUES(description)
            ");
            $stmt->bind_param("ssss", $occupation_name, $isco_code, $esco_code, $description);
            if ($stmt->execute()) $inserted++;
        }
        fclose($handle);
        echo "<div class='alert alert-success'>Imported $inserted records successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to open file.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Import ESCO/ISCO CSV</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container py-5">
  <h2>Upload ESCO/ISCO CSV</h2>
  <form method="POST" enctype="multipart/form-data" class="border p-4 shadow rounded bg-light">
    <div class="mb-3">
      <label for="csv_file" class="form-label">Choose CSV File</label>
      <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload and Import</button>
  </form>
  <p class="mt-3 text-muted">CSV Format: occupation_name, isco_code, esco_code, description</p>
</body>
</html>
