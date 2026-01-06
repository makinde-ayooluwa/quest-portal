<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Profile - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .profile-card {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
        }

        .profile-header {
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
        }

        .edit-btn {
            float: right;
        }

        .info-label {
            font-weight: 500;
            color: #198754;
        }

        .guardian-card {
            background: #f1f3f6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>
    <div class="profile-card">
        <div class="profile-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="<?php
                            echo $studentData['picture'];
                            // Example: echo "SS2A";
                            ?>" alt="Student Avatar" class="profile-avatar me-3">
                <div>
                    <h2 class="mb-0">
                        <?php
                        echo $studentData['fullname'];
                        // Example: echo "John Doe";
                        ?>
                    </h2>
                    <p class="mb-0 text-muted">Class: <span class="fw-bold">
                            <?php
                            echo $studentData['class'];
                            // Example: echo "John Doe";
                            ?>
                        </span></p>
                    <!--<p class="mb-0 text-muted">Roll No: <span class="fw-bold">
                        
                    </span></p>-->
                </div>
            </div>
            <a href="./profile_edit.php" class="btn btn-primary edit-btn"><i class="bi bi-pencil me-1"></i></a>
        </div>
        <h4 class="mt-4 mb-3">Personal Details</h4>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <span class="info-label">Date of Birth:</span> <?php
                                                                echo $studentData['dob'];
                                                                // Example: echo "John Doe";
                                                                ?>
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Gender:</span> <?php
                                                        echo $studentData['gender'];
                                                        // Example: echo "John Doe";
                                                        ?>
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Email:</span> <?php
                                                        echo $studentData['email'];
                                                        // Example: echo "John Doe";
                                                        ?>
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Phone:</span> <?php
                                                        echo $studentData['phone'];
                                                        // Example: echo "John Doe";
                                                        ?>
            </div>
            <div class="col-md-12 mb-2">
                <span class="info-label">Address:</span> <?php
                                                            echo $studentData['home_address'];
                                                            // Example: echo "12, Quest Avenue, Lagos, Nigeria";
                                                            ?>
            </div>
        </div>
        <h4 class="mt-4 mb-3">Guardian Information</h4>
        <div class="guardian-card">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <span class="info-label">Name:</span><?php
                                                            echo $studentData['mother_name'];
                                                            // Example: echo "12, Quest Avenue, Lagos, Nigeria";
                                                            ?>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Relationship:</span> Mother
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Phone:</span> <?php
                                                            echo $studentData['mother_phone'];
                                                            // Example: echo "12, Quest Avenue, Lagos, Nigeria";
                                                            ?>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Email:</span> <?php
                                                            echo $studentData['mother_email'];
                                                            // Example: echo "janedoe@email.com";
                                                            ?>
                </div>
            </div>
        </div>
        <div class="guardian-card">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <span class="info-label">Name:</span> <?php
                                                            echo $studentData['father_name'];
                                                            // Example: echo "12, Quest Avenue, Lagos, Nigeria";
                                                            ?>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Relationship:</span> Father
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Phone:</span> <?php
                                                            echo $studentData['father_phone'];
                                                            // Example: echo "12, Quest Avenue, Lagos, Nigeria";
                                                            ?>
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Email:</span> <?php
                                                            echo $studentData['father_email'];
                                                            // Example: echo "richarddoe@email.com";
                                                            ?>
                </div>
            </div>
        </div>
        <h4 class="mt-4 mb-3">Academic Summary</h4>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <!--<span class="info-label">Current GPA:</span> 4.2-->
            </div>
            <div class="col-md-4 mb-2">
                <span class="info-label">Attendance:</span> 97%
            </div>
        </div>
        <!--<h4 class="mt-4 mb-3">Quick Actions</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="result.php" class="btn btn-success"><i class="bi bi-bar-chart me-1"></i>View Results</a>
            <a href="assignments.php" class="btn btn-info text-white"><i class="bi bi-book me-1"></i>Assignments</a>
            <a href="materials.php" class="btn btn-warning text-dark"><i class="bi bi-cloud-download me-1"></i>Study Materials</a>
            <a href="notifications.php" class="btn btn-secondary"><i class="bi bi-bell me-1"></i>Notifications</a>
            <a href="support.php" class="btn btn-dark"><i class="bi bi-question-circle me-1"></i>Support</a>
        </div>-->

    </div>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
