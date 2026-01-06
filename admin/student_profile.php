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
    <div class="profile-card">
        <div class="profile-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="../assets/images/student-avatar.png" alt="Student Avatar" class="profile-avatar me-3">
                <div>
                    <h2 class="mb-0">John Doe</h2>
                    <p class="mb-0 text-muted">Class: <span class="fw-bold">Math 101</span></p>
                    <p class="mb-0 text-muted">Roll No: <span class="fw-bold">2025001</span></p>
                </div>
            </div>
            <!--<a href="profile_edit.php" class="btn btn-primary edit-btn"><i class="bi bi-pencil me-1"></i>Edit Profile</a>-->
        </div>
        <h4 class="mt-4 mb-3">Personal Details</h4>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <span class="info-label">Date of Birth:</span> 2008-05-14
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Gender:</span> Male
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Email:</span> johndoe@email.com
            </div>
            <div class="col-md-6 mb-2">
                <span class="info-label">Phone:</span> +234 801 234 5678
            </div>
            <div class="col-md-12 mb-2">
                <span class="info-label">Address:</span> 12, Quest Avenue, Lagos, Nigeria
            </div>
        </div>
        <h4 class="mt-4 mb-3">Guardian Information</h4>
        <div class="guardian-card">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <span class="info-label">Name:</span> Mrs. Jane Doe
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Relationship:</span> Mother
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Phone:</span> +234 802 345 6789
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Email:</span> janedoe@email.com
                </div>
            </div>
        </div>
        <div class="guardian-card">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <span class="info-label">Name:</span> Mr. Richard Doe
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Relationship:</span> Father
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Phone:</span> +234 803 456 7890
                </div>
                <div class="col-md-6 mb-2">
                    <span class="info-label">Email:</span> richarddoe@email.com
                </div>
            </div>
        </div>
        <h4 class="mt-4 mb-3">Academic Summary</h4>
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <span class="info-label">Current GPA:</span> 4.2
            </div>
            <div class="col-md-4 mb-2">
                <span class="info-label">Attendance:</span> 97%
            </div>
            <div class="col-md-4 mb-2">
                <span class="info-label">Scholarship:</span> Awarded
            </div>
        </div>
        <!-- Add this section below Academic Summary and above Quick Actions -->
        <h4 class="mt-4 mb-3">Attendance & Academic Monitoring</h4>
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <form action="upload_results.php" method="post" enctype="multipart/form-data" class="d-flex gap-2">
                    <input type="file" name="result_file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="form-control" required>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-upload me-1"></i>Upload Results
                    </button>
                </form>
            </div>
            <div class="col-md-6 mb-2">
                <a href="attendance_report.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-bar-chart-line me-1"></i>Generate Attendance/Assessment Report
                </a>
            </div>
        </div>
        <h4 class="mt-4 mb-3">Quick Actions</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="../results.php" class="btn btn-success"><i class="bi bi-bar-chart me-1"></i>View Results</a>
            <a href="../assignments.php" class="btn btn-info text-white"><i class="bi bi-book me-1"></i>Assignments</a>
            <a href="../materials.php" class="btn btn-warning text-dark"><i class="bi bi-cloud-download me-1"></i>Study Materials</a>
            <a href="../notifications.php" class="btn btn-secondary"><i class="bi bi-bell me-1"></i>Notifications</a>
            <a href="../support.php" class="btn btn-dark"><i class="bi bi-question-circle me-1"></i>Support</a>
        </div>
    </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>