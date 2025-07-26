<canvas id="applicationsChart" height="100"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('applicationsChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar'], // Replace with dynamic PHP values
        datasets: [{
            label: 'Applications',
            data: [12, 19, 7],
            backgroundColor: '#17a2b8'
        }]
    }
});
</script>
