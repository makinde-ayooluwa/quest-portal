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
        body {
            background: #f8f9fa;
        }

        .profile-card {
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            animation: fadeInUp 0.5s ease forwards;
            overflow: hidden;
            position: relative;
        }
        .profile-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .profile-avatar {
            width: 120px; height: 120px; object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--quest-green-100);
            box-shadow: 0 4px 16px rgba(90, 172, 123, 0.3);
            transition: transform var(--transition-base), box-shadow var(--transition-base);
        }
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(90, 172, 123, 0.4);
            cursor: pointer;
        }

        .profile-header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--slate-100);
        }

        .section-title-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.125rem;
            margin-bottom: 1rem;
            color: var(--slate-800);
        }
        .section-title-profile::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--slate-200), transparent);
            margin-left: 0.5rem;
        }

        .info-item {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.9375rem;
        }
        .info-label {
            font-weight: 600;
            color: var(--quest-green-700);
            min-width: 140px;
        }
        .info-value {
            color: var(--slate-700);
        }

        .guardian-card {
            background: linear-gradient(135deg, var(--quest-green-50), var(--slate-50));
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid var(--quest-green-200);
            transition: all var(--transition-base);
        }
        .guardian-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--quest-green-300);
        }

        .edit-profile-btn {
            width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            background: var(--quest-green-50);
            color: var(--quest-green-700);
            border: 1px solid var(--quest-green-200);
            transition: all var(--transition-base);
        }
        .edit-profile-btn:hover {
            background: var(--quest-green-400);
            color: #fff;
            border-color: var(--quest-green-400);
            transform: scale(1.1);
            box-shadow: var(--shadow-glow-green);
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>
    <div class="container-fluid pt-4">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <div class="profile-card">
                    <div class="profile-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img data-bs-toggle="modal" data-bs-target="#profile-image"
                                src="<?php echo $studentData['picture']; ?>" alt="Student Avatar" class="profile-avatar me-4">
                            <div id="profile-image" class="modal fade" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-transparent border-0">
                                        <img src="<?php echo $studentData['picture']; ?>" alt="Student Avatar" class="rounded-4 shadow-xl">
                                    </div>
                            </div>
                            <div>
                                <h2 class="mb-1 fw-bold"><?php echo $studentData['fullname']; ?></h2>
                                <p class="mb-0 text-muted">Class: <span class="badge badge-modern badge-secondary"><?php echo $studentData['class']; ?></span></p>
                            </div>
                        <a href="./profile_edit.php" class="edit-profile-btn" title="Edit Profile">
                            <i class="bi bi-pencil-fill fs-5"></i>
                        </a>
                    </div>

                    <div class="section-title-profile"><i class="bi bi-person text-green"></i>Personal Details</div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-item"><span class="info-label">Date of Birth:</span><span class="info-value"><?php echo $studentData['dob']; ?></span></div>
                            <div class="info-item"><span class="info-label">Gender:</span><span class="info-value"><?php echo $studentData['gender']; ?></span></div>
                        <div class="col-md-6">
                            <div class="info-item"><span class="info-label">Email:</span><span class="info-value"><?php echo $studentData['email']; ?></span></div>
                            <div class="info-item"><span class="info-label">Phone:</span><span class="info-value"><?php echo $studentData['phone']; ?></span></div>
                        <div class="col-md-12">
                            <div class="info-item"><span class="info-label">Address:</span><span class="info-value"><?php echo $studentData['home_address']; ?></span></div>
                    </div>

                    <div class="section-title-profile"><i class="bi bi-people-fill text-green"></i>Guardian Information</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="guardian-card">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-person-standing text-green"></i>
                                    <strong class="text-slate-800">Mother</strong>
                                </div>
                                <div class="info-item"><span class="info-label">Name:</span><span class="info-value"><?php echo $studentData['mother_name']; ?></span></div>
                                <div class="info-item"><span class="info-label">Phone:</span><span class="info-value"><?php echo $studentData['mother_phone']; ?></span></div>
                                <div class="info-item"><span class="info-label">Email:</span><span class="info-value"><?php echo $studentData['mother_email']; ?></span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="guardian-card">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-person-standing text-green"></i>
                                    <strong class="text-slate-800">Father</strong>
                                </div>
                                <div class="info-item"><span class="info-label">Name:</span><span class="info-value"><?php echo $studentData['father_name']; ?></span></div>
                                <div class="info-item"><span class="info-label">Phone:</span><span class="info-value"><?php echo $studentData['father_phone']; ?></span></div>
                                <div class="info-item"><span class="info-label">Email:</span><span class="info-value"><?php echo $studentData['father_email']; ?></span></div>
                        </div>
                </div>
        </div>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
