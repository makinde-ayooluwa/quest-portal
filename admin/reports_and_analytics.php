<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Quest Schools Admin</title>
    <!--Fonts-->
  <link rel="stylesheet" href="css/fonts.min.css">
  <!--Favicon-->
  <link rel="shortcut icon" href="assets/images/Quest logo.jpg" type="image/x-icon">
  <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=fire">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">-->
  <!--Styles-->
  <link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <!--Scripts-->
  <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery.min.js"></script>
    <style>
        * {
            font-family: Montserrat;
        }
        body { background: #f8f9fa; }
        .analytics-card {
            max-width: 1100px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 2rem;
        }
        .chart-container {
            max-width: 600px;
            margin: 2rem auto;
        }
    </style>
</head>
<body>
    <div class="analytics-card">
        <h2 class="mb-4 text-center"><i class="bi bi-bar-chart-line me-2"></i>Reports & Analytics Dashboard</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card p-3">
                    <h5 class="mb-3">Performance Trend</h5>
                    <canvas id="performanceChart" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card p-3">
                    <h5 class="mb-3">Retention Rate</h5>
                    <canvas id="retentionChart" height="180"></canvas>
                </div>
            </div>
            <div class="col-md-12 mb-4">
                <div class="card p-3">
                    <h5 class="mb-3">Engagement Overview</h5>
                    <canvas id="engagementChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <h5>Summary Table</h5>
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Metric</th>
                        <th>Current Value</th>
                        <th>Change</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Average Performance</td>
                        <td>82%</td>
                        <td><span class="text-success"><i class="bi bi-arrow-up"></i> +3%</span></td>
                        <td>2025-09-22</td>
                    </tr>
                    <tr>
                        <td>Retention Rate</td>
                        <td>92%</td>
                        <td><span class="text-success"><i class="bi bi-arrow-up"></i> +2%</span></td>
                        <td>2025-09-22</td>
                    </tr>
                    <tr>
                        <td>Engagement Score</td>
                        <td>78%</td>
                        <td><span class="text-danger"><i class="bi bi-arrow-down"></i> -1%</span></td>
                        <td>2025-09-22</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Performance Trend (Line Chart)
        new Chart(document.getElementById('performanceChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                datasets: [{
                    label: 'Performance (%)',
                    data: [78, 80, 79, 81, 83, 82, 84, 85, 82],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });

        // Retention Rate (Bar Chart)
        new Chart(document.getElementById('retentionChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['2021', '2022', '2023', '2024', '2025'],
                datasets: [{
                    label: 'Retention (%)',
                    data: [85, 87, 88, 90, 92],
                    backgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });

        // Engagement Overview (Pie Chart)
        new Chart(document.getElementById('engagementChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Assignments', 'Attendance', 'Events', 'Competitions'],
                datasets: [{
                    data: [30, 40, 15, 15],
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>
</html>