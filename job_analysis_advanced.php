<?php
include('header.php');
include('db_connection.php');

$country = $_GET['country'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

$whereClauses = [];
$params = [];

if ($country !== '') {
    $whereClauses[] = "location_country = ?";
    $params[] = $country;
}
if ($fromDate !== '') {
    $whereClauses[] = "posted_date >= ?";
    $params[] = $fromDate;
}
if ($toDate !== '') {
    $whereClauses[] = "posted_date <= ?";
    $params[] = $toDate;
}

$whereSQL = $whereClauses ? "WHERE " . implode(' AND ', $whereClauses) : "";

function fetchData($conn, $sql, $params) {
    $stmt = $conn->prepare($sql);
    if ($params) $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$charts = [];
$queries = [
    'jobs_by_country'  => "SELECT location_country AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY location_country ORDER BY total DESC",
    'jobs_by_type'     => "SELECT job_type AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY job_type ORDER BY total DESC",
    'jobs_by_currency' => "SELECT currency AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY currency ORDER BY total DESC",
    'jobs_by_status'   => "SELECT status AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY status ORDER BY total DESC"
];

foreach ($queries as $key => $sql) {
    $charts[$key] = fetchData($conn, $sql . " $whereSQL", $params);
}

if (isset($_GET['export'])) {
    require 'vendor/autoload.php';
    $sheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $row = 1;
    foreach ($charts as $key => $data) {
        $sheet->setActiveSheetIndex(0)
              ->setCellValue("A$row", ucfirst(str_replace('_', ' ', $key)));
        $row++;
        foreach ($data as $i => $r) {
            $sheet->setActiveSheetIndex(0)
                  ->setCellValue("A".(++$row), $r['label'])
                  ->setCellValue("B".($row), $r['total']);
        }
        $row += 2;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="job_analysis.xlsx"');
    \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($sheet, 'Xlsx')->save('php://output');
    exit;
}
?>

<form method="get">
  Country: <input type="text" name="country" value="<?=htmlspecialchars($country)?>">
  From: <input type="date" name="from" value="<?=$fromDate?>">
  To: <input type="date" name="to" value="<?=$toDate?>">
  <button type="submit">Apply Filters</button>
  <button type="button" onclick="location.href='job_analysis.php'">Reset</button>
  <button type="submit" name="export" value="1">📤 Export to Excel</button>
</form>

<pre><?php print_r($charts); ?></pre>

<?php include('footer.php'); ?>
