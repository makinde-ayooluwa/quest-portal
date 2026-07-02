<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Edit Profile - Quest Schools</title>

    <style>
        body {
            background: #f8f9fa;
        }

        .edit-card {
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
        .edit-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .profile-avatar {
            width: 100px; height: 100px; object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--quest-green-100);
            box-shadow: 0 4px 16px rgba(90, 172, 123, 0.3);
        }

        .section-title-edit {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.125rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: var(--slate-800);
        }
        .section-title-edit::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--slate-200), transparent);
            margin-left: 0.5rem;
        }

        .form-control:focus {
            border-color: var(--quest-green);
            box-shadow: 0 0 0 0.2rem rgba(90, 172, 123, 0.15);
        }
        .form-select:focus {
            border-color: var(--quest-green);
            box-shadow: 0 0 0 0.2rem rgba(90, 172, 123, 0.15);
        }

        .modal-header {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
            color: #fff;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }

        .btn-save {
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff; border: none;
            transition: all var(--transition-base);
            position: relative; overflow: hidden;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-green);
            color: #fff;
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
                <?php
                if (isset($_SESSION["error"])) {
                ?>
                    <script>
                        toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");
                    </script>
                <?php
                    unset($_SESSION["error"]);
                } elseif (isset($_SESSION["success"])) {
                ?>
                    <script>
                        toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>", "Success!");
                    </script>
                <?php
                }
                ?>
                <div class="edit-card card-modern">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?php echo $studentData['picture']; ?>" alt="Student Avatar" class="profile-avatar me-3">
                        <div>
                            <h2 class="mb-0 fw-bold">Edit Profile</h2>
                            <p class="mb-0 text-muted">Class: <span class="badge badge-modern badge-secondary"><?php echo $studentData['class']; ?></span></p>
                        </div>
                    <form action="profile_edit_handler.php" method="POST" enctype="multipart/form-data">
                        <div class="section-title-edit"><i class="bi bi-person text-green"></i>Personal Details</div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option selected><?php echo $studentData['gender']; ?></option>
                                    <option><?php
                                            switch ($studentData['gender']) {
                                                case "Male":
                                                    echo "Female";
                                                    break;
                                                case "Female":
                                                    echo "Male";
                                                    break;
                                            }
                                            ?></option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Phone</label>
                                <input name="phone" type="tel" class="form-control" value="<?php echo $studentData['phone']; ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Address</label>
                                <input name="home_address" type="text" class="form-control"
                                    value="<?php echo $studentData['home_address']; ?>">
                            </div>

                        <div class="section-title-edit"><i class="bi bi-people-fill text-green"></i>Parent / Guardian Information</div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Father's Name</label>
                                <input name="father_name" type="text" class="form-control"
                                    value="<?php echo $studentData['father_name']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Father's Phone</label>
                                <input name="father_phone" type="tel" class="form-control"
                                    value="<?php echo $studentData['father_phone']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Father's Email</label>
                                <input name="father_email" type="email" class="form-control"
                                    value="<?php echo $studentData['father_email']; ?>">
                            </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mother's Name</label>
                                <input name="mother_name" type="text" class="form-control"
                                    value="<?php echo $studentData['mother_name']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mother's Phone</label>
                                <input name="mother_phone" type="tel" class="form-control"
                                    value="<?php echo $studentData['mother_phone']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mother's Email</label>
                                <input name="mother_email" type="email" class="form-control"
                                    value="<?php echo $studentData['mother_email']; ?>">
                            </div>

                        <!-- Confirm Modal -->
                        <div class="modal fade" id="confirmModal">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Confirm Changes</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-danger fs-5">Enter your password to save changes</p>
                                        <p>For your account security, password is required to make changes in your profile.</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Password</label>
                                            <input autofocus name="password" type="password" class="form-control" required>
                                        </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-save"><i class="bi bi-save me-2"></i>Save Changes</button>
                                    </div>
                            </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="profile.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Cancel</a>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#confirmModal" class="btn btn-save"><i
                                    class="bi bi-save me-2"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
        </div>
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
