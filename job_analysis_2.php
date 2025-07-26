<?php
include 'auth_check.php';
include 'header.php';

$pdo = new PDO("mysql:host=localhost;dbname=moodledatabase", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Get counts
$total_jobs = $pdo->query("SELECT COUNT(*) FROM lmis3_jobs")->fetchColumn();

$jobs_by_country = $pdo->query("
    SELECT location_country AS country, COUNT(*) AS count
    FROM lmis3_jobs GROUP BY location_country
")->fetchAll(PDO::FETCH_ASSOC);

$jobs_by_type = $pdo->query("
    SELECT job_type, COUNT(*) AS count
    FROM lmis3_jobs GROUP BY job_type
")->fetchAll(PDO::FETCH_ASSOC);

$jobs_by_currency = $pdo->query("
    SELECT currency, COUNT(*) AS count
    FROM lmis3_jobs GROUP BY currency
")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>📈 Job Analytics Dashboard</h2>

<p>Total Jobs: <strong><?= $total_jobs ?></strong></p>

<canvas id="chartCountry" width="600" height="300"></canvas>
<canvas id="chartType" width="600" height="300"></canvas>
<canvas id="chartCurrency" width="600" height="300"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const countryCtx = document.getElementById('chartCountry').getContext('2d');
const typeCtx = document.getElementById('chartType').getContext('2d');
const currencyCtx = document.getElementById('chartCurrency').getContext('2d');

new Chart(countryCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($jobs_by_country, 'country')) ?>,
        datasets: [{
            label: 'Jobs by Country',
            data: <?= json_encode(array_column($jobs_by_country, 'count')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    }
});

new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($jobs_by_type, 'job_type')) ?>,
        datasets: [{
            label: 'Jobs by Type',
            data: <?= json_encode(array_column($jobs_by_type, 'count')) ?>,
            backgroundColor: ['#ff6384','#36a2eb','#cc65fe','#ffce56','#00b894']
        }]
    }
});

new Chart(currencyCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($jobs_by_currency, 'currency')) ?>,
        datasets: [{
            label: 'Jobs by Currency',
            data: <?= json_encode(array_column($jobs_by_currency, 'count')) ?>,
            backgroundColor: ['#6c5ce7','#00cec9','#fab1a0']
        }]
    }
});
</script>

<?php include 'footer.php'; ?>
