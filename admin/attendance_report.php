<!DOCTYPE html>
<html lang="en">
<head>
    <title>Attendance & Assessment Report - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }
        body { background: #f8f9fa; }
        .report-card {
            max-width: 900px;
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
    <div class="report-card">
        <h2 class="mb-4 text-center"><i class="bi bi-bar-chart-line me-2"></i>Attendance & Assessment Report</h2>
        <form class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="selectClass" class="form-label">Class</label>
                <select class="form-select" id="selectClass">
                    <option>Math 101</option>
                    <option>Physics 201</option>
                    <option>Chemistry 301</option>
                    <option>English 102</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="selectTerm" class="form-label">Term</label>
                <select class="form-select" id="selectTerm">
                    <option>First Term</option>
                    <option>Second Term</option>
                    <option>Third Term</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100"><i class="bi bi-search"></i> Generate Report</button>
            </div>
        </form>
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Attendance (%)</th>
                        <th>Assessment Score</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>John Doe</td>
                        <td>2025001</td>
                        <td>97</td>
                        <td>88</td>
                        <td>A</td>
                    </tr>
                    <tr>
                        <td>Emily Clark</td>
                        <td>2025002</td>
                        <td>95</td>
                        <td>75</td>
                        <td>B</td>
                    </tr>
                    <tr>
                        <td>Michael Lee</td>
                        <td>2025003</td>
                        <td>92</td>
                        <td>92</td>
                        <td>A+</td>
                    </tr>
                    <tr>
                        <td>Sarah Brown</td>
                        <td>2025004</td>
                        <td>81</td>
                        <td>81</td>
                        <td>B+</td>
                    </tr>
                    <tr>
                        <td>Akinleye Mathias</td>
                        <td>2025005</td>
                        <td>88</td>
                        <td>69</td>
                        <td>C+</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="chart-container">
            <canvas id="attendanceChart" height="120"></canvas>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-success"><i class="bi bi-file-earmark-pdf me-1"></i>Download Report as PDF</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Attendance Chart Example (Bar Chart)
        new Chart(document.getElementById('attendanceChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['John Doe', 'Emily Clark', 'Michael Lee', 'Sarah Brown','Akinleye Mathias'],
                datasets: [{
                    label: 'Attendance (%)',
                    data: [97, 95, 92, 81, 88],
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    </script>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>
</html>