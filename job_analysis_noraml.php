<?php
// Error reporting for debugging (turn off in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('header.php');
include('db_connection.php');

if (!isset($conn)) {
    die("Database connection not set - check db_connection.php");
}

// Queries to get counts by various categories
$queries = [
    'jobs_by_country'  => "SELECT location_country AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY location_country ORDER BY total DESC",
    'jobs_by_type'     => "SELECT job_type AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY job_type ORDER BY total DESC",
    'jobs_by_currency' => "SELECT currency AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY currency ORDER BY total DESC",
    'jobs_by_status'   => "SELECT status AS label, COUNT(*) AS total FROM lmis3_jobs GROUP BY status ORDER BY total DESC"
];

$charts = [];

foreach ($queries as $key => $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $charts[$key] = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $charts[$key][] = $row;
        }
        mysqli_free_result($result);
    } else {
        $charts[$key] = [];
    }
}

// Close connection if you want (optional)
// mysqli_close($conn);
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
        drawChart(<?= json_encode($charts['jobs_by_country']) ?>, 'Jobs by Country', 'country_chart');
        drawChart(<?= json_encode($charts['jobs_by_type']) ?>, 'Jobs by Type', 'type_chart');
        drawChart(<?= json_encode($charts['jobs_by_currency']) ?>, 'Jobs by Currency', 'currency_chart');
        drawChart(<?= json_encode($charts['jobs_by_status']) ?>, 'Jobs by Status', 'status_chart');
    }

    function drawChart(data, title, elementId) {
        const chartData = new google.visualization.DataTable();
        chartData.addColumn('string', 'Label');
        chartData.addColumn('number', 'Total');

        data.forEach(row => {
            chartData.addRow([row.label || 'Unknown', parseInt(row.total)]);
        });

        const options = {
            title: title,
            pieHole: 0.4,
            width: 500,
            height: 300
        };

        const chart = new google.visualization.PieChart(document.getElementById(elementId));
        chart.draw(chartData, options);
    }
    </script>
</head>
<body>
    <h2 style="text-align: center;">📊 Job Market Analysis Dashboard</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; padding: 20px;">
        <div id="country_chart"></div>
        <div id="type_chart"></div>
        <div id="currency_chart"></div>
        <div id="status_chart"></div>
    </div>
</body>
</html>

<?php include('footer.php'); ?>
