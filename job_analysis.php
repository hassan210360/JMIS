<?php include('header.php');?>
<?php
// Database connection
$host = 'localhost';
$db   = 'moodledatabase';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Group job counts
    $charts = [];

    $queries = [
        'jobs_by_country' => "SELECT location_country AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY location_country ORDER BY total DESC",
        'jobs_by_type'    => "SELECT job_type AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY job_type ORDER BY total DESC",
        'jobs_by_currency'=> "SELECT currency AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY currency ORDER BY total DESC",
        'jobs_by_status'  => "SELECT status AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY status ORDER BY total DESC"
    ];

    foreach ($queries as $key => $sql) {
        $stmt = $pdo->query($sql);
        $charts[$key] = $stmt->fetchAll();
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Market Dashboard</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {'packages':['corechart']});

        google.charts.setOnLoadCallback(drawAllCharts);

        function drawAllCharts() {
            drawChart(<?= json_encode($charts['jobs_by_country']) ?>, 'Country Distribution', 'country_chart');
            drawChart(<?= json_encode($charts['jobs_by_type']) ?>, 'Jobs by Type', 'type_chart');
            drawChart(<?= json_encode($charts['jobs_by_currency']) ?>, 'Jobs by Currency', 'currency_chart');
            drawChart(<?= json_encode($charts['jobs_by_status']) ?>, 'Jobs by Status', 'status_chart');
        }

        function drawChart(data, title, elementId) {
            const chartData = new google.visualization.DataTable();
            chartData.addColumn('string', 'Label');
            chartData.addColumn('number', 'Count');

            data.forEach(row => {
                chartData.addRow([row.label || 'Undefined', parseInt(row.total)]);
            });

            const options = {
                title: title,
                width: 500,
                height: 300,
                pieHole: 0.4
            };

            const chart = new google.visualization.PieChart(document.getElementById(elementId));
            chart.draw(chartData, options);
        }
    </script>
</head>
<body>
    <h1>📊 Job Market Analysis Dashboard</h1>
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <div id="country_chart"></div>
        <div id="type_chart"></div>
        <div id="currency_chart"></div>
        <div id="status_chart"></div>
    </div>
</body>
</html>
<?php include('footer.php');?>