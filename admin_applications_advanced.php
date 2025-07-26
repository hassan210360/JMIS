<?php
include 'header.php';
include 'db_connection.php';

$filter_job = $_GET['job'] ?? '';
$filter_user_type = $_GET['user_type'] ?? '';
$filter_date_from = $_GET['from'] ?? '';
$filter_date_to = $_GET['to'] ?? '';

// Base SQL
$sql = "SELECT a.*, j.title FROM lmis3_job_applications a JOIN lmis3_jobs j ON a.job_id = j.job_id WHERE 1";

// Filters
$params = [];
if ($filter_job) {
    $sql .= " AND j.title LIKE ?";
    $params[] = "%$filter_job%";
}
if ($filter_user_type) {
    $sql .= " AND a.user_type = ?";
    $params[] = $filter_user_type;
}
if ($filter_date_from && $filter_date_to) {
    $sql .= " AND DATE(a.applied_at) BETWEEN ? AND ?";
    $params[] = $filter_date_from;
    $params[] = $filter_date_to;
}

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h3>📄 Applications (Admin View)</h3>

    <form method="get" class="row g-2 my-3">
        <div class="col-md-3"><input type="text" name="job" class="form-control" placeholder="Job Title" value="<?= htmlspecialchars($filter_job) ?>"></div>
        <div class="col-md-3">
            <select name="user_type" class="form-control">
                <option value="">All Users</option>
                <option value="jobseeker" <?= $filter_user_type === 'jobseeker' ? 'selected' : '' ?>>Jobseekers</option>
                <option value="guest" <?= $filter_user_type === 'guest' ? 'selected' : '' ?>>Guests</option>
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control" value="<?= htmlspecialchars($filter_date_from) ?>"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control" value="<?= htmlspecialchars($filter_date_to) ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filter</button></div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Job</th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr class="<?= $row['user_type'] === 'guest' ? 'table-warning' : '' ?>">
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['user_type'] ?></td>
                <td><?= $row['applied_at'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
