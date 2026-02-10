<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";

define("STUDENT_DATA", $studentData);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> <?php echo $studentData['fullname'] ?> | Quest Schools - Student Portal</title>
    <?php include "head.php" ?>
    <style>
        .form-container {
            width: 482px;
            border-radius: 12px;
            margin-top: 20px;
        }

        * {
            font-family: Montserrat;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <div id="searchSuggestions" class="list-group position-absolute w-100" style="z-index: 999; display: none;"></div>
    <?php include "sidebar.php" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 main-bar">
                <marquee on direction="rtl" class="text-center py-4 text-green">Welcome back <?php
                                                                                                echo $studentData["fullname"];
                                                                                                ?>. Good to
                    have
                    you back.</marquee>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 p-4 mb-4"
                            style="background: linear-gradient(90deg, var(--quest-green) 60%, var(--quest-yellow) 100%); color: #fff;">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                <div>
                                    <h2 class="mb-1">Welcome, <?php
                                                                echo $studentData['fullname'];
                                                                // Example: echo "SS2A";
                                                                ?>!</h2>
                                    <p class="mb-0">Current Class: <span class="fw-bold text-yellow">
                                            <?php
                                            echo STUDENT_DATA["class"];
                                            ?>
                                        </span></p>
                                </div>
                                <!-- <div class="mt-3 mt-md-0">
                                    <span class="me-4"><i class="bi bi-people-fill me-2"></i>Students: <b>35</b></span>
                                    <span class="me-4"><i class="bi bi-person-badge me-2"></i>Mentors: <b class="fs-5">
                                            <?php
                                            $studentClass = $studentData["class"]; // student's class

                                            $query = "SELECT * FROM classes WHERE class_name = :student_class";
                                            $stmt = $pdo->prepare($query);
                                            $stmt->bindParam(":student_class", $studentClass);
                                            $stmt->execute();

                                            $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            echo count($staffs);
                                            ?>
                                        </b></span>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- Academic Progress Chart Section -->
                    <!--<div class="col-md-6"
                        style="padding:20px;background:#f9f9f9;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                        <h2 style="text-align:center;">Academic Progress Chart</h2>
                        <canvas id="progressChart" width="600" height="350"></canvas>
                    </div>-->

                    <!-- Chart.js CDN -->
                    <!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        const ctx = document.getElementById('progressChart').getContext('2d');
                        const progressChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Math', 'Commerce', 'Science', 'Financial Accounting', 'English', 'History', 'Art', 'Economics', 'Physics', 'Biology', 'Chemistry'],
                                datasets: [{
                                    label: 'Score (%)',
                                    data: [85, 92, 78, 88, 99, 100, 95, 65, 78, 50, 89],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)',
                                        'rgba(153, 102, 255, 0.7)'
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    },
                                    title: {
                                        display: false
                                    }
                                }
                            }
                        });
                    </script>-->
                    <!-- Upcoming Assignments Table Section -->
                    <!--<div class="col-md-6"
                        style="background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-top:20px;">
                        <h2 style="text-align:center;">Upcoming Assignments</h2>
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th>Subject</th>
                                    <th>Assignment</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Math</td>
                                    <td>Algebra Worksheet</td>
                                    <td>2025-09-25</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>Science</td>
                                    <td>Lab Report</td>
                                    <td>2025-09-28</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>English</td>
                                    <td>Essay: Modern Literature</td>
                                    <td>2025-09-30</td>
                                    <td><span class="badge bg-success">Submitted</span></td>
                                </tr>
                                <tr>
                                    <td>History</td>
                                    <td>Project: Ancient Civilizations</td>
                                    <td>2025-10-02</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>-->
                    <!-- Recent Notifications List Section -->
                    <!--<div class="col-md-12"
                        style="background:#f8f9fa;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);margin-top:20px;padding:20px;">
                        <h2 style="text-align:center;">Recent Notifications</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-bell text-yellow me-2"></i>
                                <span>Math assignment deadline extended to 2025-09-28.</span>
                                <span class="ms-auto text-muted small">2 hours ago</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-bell text-yellow me-2"></i>
                                <span>Science lab report feedback available.</span>
                                <span class="ms-auto text-muted small">5 hours ago</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-bell text-yellow me-2"></i>
                                <span>New assignment posted for History.</span>
                                <span class="ms-auto text-muted small">Yesterday</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fa-solid fa-bell text-yellow me-2"></i>
                                <span>English essay submission confirmed.</span>
                                <span class="ms-auto text-muted small">2 days ago</span>
                            </li>
                        </ul>
                    </div>-->
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-person-fill me-2"></i>Profile Management</h5>
                                <ul>
                                    <li>View &amp; edit personal details</li>
                                    <li>Update contact info and guardians</li>
                                </ul>
                                <a href="profile_edit.php" class="btn btn-primary btn-sm mt-2"><i class="bi bi-pencil"></i> Edit
                                    Profile</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-journal-text me-2"></i>Academic Records</h5>
                                <ul>
                                    <li>Access results &amp; grades</li>
                                    <li>View attendance reports</li>
                                </ul>
                                <a href="result.php" class="btn btn-success btn-sm mt-2"><i class="bi bi-bar-chart"></i> View
                                    Results</a>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-book me-2"></i>Assignments &amp; Materials</h5>
                                <ul>
                                    <li>Download/upload assignments</li>
                                    <li>Access LEMA or course content</li>
                                </ul>
                                <button class="btn btn-info btn-sm mt-2" disabled><i class="bi bi-cloud-download"></i>
                                    Download
                                    Materials</button>
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-award me-2"></i>Scholarship/Program Status</h5>
                                <ul>
                                    <li>Track application status</li>
                                    <li>View acceptance updates</li>
                                </ul>
                                <button class="btn btn-warning btn-sm mt-2" disabled><i class="bi bi-search"></i> Check
                                    Status</button>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-bell me-2"></i>Notifications</h5>
                                <ul>
                                    <li>Receive updates on exams, events, competitions</li>
                                </ul>
                                <a class="btn btn-secondary btn-sm mt-2" href="notifications.php"><i class="bi bi-bell"></i> View
                                    Notifications</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-question-circle me-2"></i>Support/Help Desk</h5>
                                <ul>
                                    <li>Raise requests or concerns to admin/mentors</li>
                                </ul>
                                <a href="support.php" class="btn btn-dark btn-sm mt-2"><i class="bi bi-envelope"></i> Get
                                    Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
