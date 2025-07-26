<?php
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ISCO Explorer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
  <h2>Explore ISCO Classifications</h2>
  <form method="GET" class="mb-4">
    <input type="text" name="q" placeholder="Search by ISCO code or title..." class="form-control" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  </form>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ISCO Code</th>
        <th>Occupation</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $query = "%" . ($_GET['q'] ?? '') . "%";
      $stmt = $conn->prepare("SELECT * FROM lmis3_occupations_table WHERE isco_code LIKE ? OR occupation_name LIKE ? ORDER BY isco_code");
      $stmt->bind_param("ss", $query, $query);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['isco_code']) ?></td>
          <td><?= htmlspecialchars($row['occupation_name']) ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>