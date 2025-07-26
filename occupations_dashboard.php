<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'auth_check.php';

if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'jobseeker')) {
    header("Location: unauthorized.php");
    exit();
}

require_once 'db_connection.php';

// Pagination params
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search params
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare search filter SQL and parameters
$search_sql = '';
$params = [];
$param_types = '';

if ($search !== '') {
    $search_sql = "WHERE occupation_name LIKE CONCAT('%', ?, '%') 
                    OR isco_code LIKE CONCAT('%', ?, '%')
                    OR esco_code LIKE CONCAT('%', ?, '%')";
    $params = [$search, $search, $search];
    $param_types = 'sss';
}

// Count total records for pagination
$count_sql = "SELECT COUNT(*) FROM lmis3_occupations_table $search_sql";
$stmt_count = $conn->prepare($count_sql);
if ($search !== '') {
    $stmt_count->bind_param($param_types, ...$params);
}
$stmt_count->execute();
$stmt_count->bind_result($total_records);
$stmt_count->fetch();
$stmt_count->close();

$total_pages = ceil($total_records / $limit);

// Fetch paginated occupations with filter
$data_sql = "SELECT occupation_id, occupation_name, isco_code, esco_code, description
             FROM lmis3_occupations_table
             $search_sql
             ORDER BY occupation_name ASC
             LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($data_sql);

if ($search !== '') {
    $stmt_data->bind_param($param_types . "ii", ...$params, $limit, $offset);
} else {
    $stmt_data->bind_param("ii", $limit, $offset);
}

$stmt_data->execute();
$result = $stmt_data->get_result();
$occupations = $result->fetch_all(MYSQLI_ASSOC);

$stmt_data->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Occupational Classification Dashboard | Egypt LMIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include 'header.php'; ?>

<div class="container my-5">
    <h1 class="mb-4">Occupational Classification Dashboard</h1>

    <form method="get" class="mb-4">
        <div class="input-group">
            <input type="search" name="search" class="form-control" placeholder="Search by occupation name, ISCO or ESCO code" value="<?= htmlspecialchars($search) ?>" />
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <p class="text-muted">Showing <?= count($occupations) ?> of <?= $total_records ?> occupations</p>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Occupation Name</th>
                    <th>ISCO Code</th>
                    <th>ESCO Code</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($occupations)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No occupations found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($occupations as $occ): ?>
                        <tr>
                            <td><?= htmlspecialchars($occ['occupation_name']) ?></td>
                            <td><?= htmlspecialchars($occ['isco_code']) ?></td>
                            <td><code><?= htmlspecialchars($occ['esco_code']) ?></code></td>
                            <td><?= nl2br(htmlspecialchars($occ['description'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $base_url = strtok($_SERVER["REQUEST_URI"], '?') . '?';
                if ($search !== '') {
                    $base_url .= 'search=' . urlencode($search) . '&';
                }

                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=' . ($page - 1) . '">&laquo; Prev</a></li>';
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active = $i === $page ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
                }

                if ($page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=' . ($page + 1) . '">Next &raquo;</a></li>';
                }
                ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
