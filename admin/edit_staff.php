<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
$id = isset($_GET["id"]) ? $_GET["id"] : null;
function getId($pdo, $id)
{
    $query = "SELECT * FROM staffs WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return true;
    } else {
        return false;
    }
}
// helper to escape output
function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
// load specific staff record for prefilling the form
$specificStaff = null;
if ($id) {
    $q = "SELECT * FROM staffs WHERE id = :id LIMIT 1";
    $s = $pdo->prepare($q);
    $s->bindParam(':id', $id);
    $s->execute();
    $specificStaff = $s->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Edit Staff/Mentor - Quest Schools Admin</title>
</head>

<body>
    <?php include "header_sidebar.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .edit-card {
            max-width: 700px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .section-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            background: #f8f9fa;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 0.5rem;
        }

        .btn-grad {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, #198754 60%, #0d6efd 100%);
            color: #fff;
        }
    </style>

    <?php
    if (!isset($_GET["id"])) {
    ?>
        <style>
            .error-container {
                max-width: 500px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff3f3;
                border: 1px solid #ffcccc;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .error-container h2 {
                color: #d9534f;
                margin-bottom: 1rem;
            }

            .error-container p {
                color: #555;
                margin-bottom: 1.5rem;
            }

            .error-container a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #d9534f;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #c9302c;
            }
        </style>
        <div class="error-container">
            <h2>Error</h2>
            <p>Sorry, the method used to request this page is not allowed.</p>
            <a href="./">Go to Homepage</a>
        </div>
    <?php
    } else if (!getId($pdo, $id)) {
    ?>
        <style>
            .error-container {
                max-width: 500px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff3f3;
                border: 1px solid #ffcccc;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .error-container h2 {
                color: #d9534f;
                margin-bottom: 1rem;
            }

            .error-container p {
                color: #555;
                margin-bottom: 1.5rem;
            }

            .error-container a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #d9534f;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #c9302c;
            }
        </style>
        <div class="error-container">
            <h2>Error</h2>
            <p>Sorry, the method used to request this page is not allowed.</p>
            <a href="./">Go to Homepage</a>
        </div>
    <?php
    } else {
        $_SESSION["staff_id"] = $id;
    ?>
        <div class="edit-card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Staff/Mentor Information</h2>
                <small class="text-light">Update staff details below</small>
            </div>
            <div class="card-body">
                <!-- Current Profile Preview -->
                <div class="text-center mb-4">
                    <div class="profile-preview">
                        <img src="<?php echo htmlspecialchars($specificStaff['picture'] ?? 'assets/images/no-picture.jpg'); ?>" alt="Current Profile" class="rounded-circle border" width="100" height="100">
                        <p class="mt-2 text-muted">Current Profile Picture</p>
                    </div>
                </div>

                <form action="edit_staff_handler.php" method="post" enctype="multipart/form-data" id="editStaffForm">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <!-- Personal Details Section -->
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-person-fill me-2"></i>Personal Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars($specificStaff['fullname'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select class="form-select" name="gender" required>
                                        <option value="Male" <?php echo (isset($specificStaff['gender']) && $specificStaff['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo (isset($specificStaff['gender']) && $specificStaff['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo (isset($specificStaff['gender']) && $specificStaff['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($specificStaff['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($specificStaff['phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Portal Code</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($specificStaff['portal_code'] ?? ''); ?>" readonly>
                                </div>
                                <small class="text-muted">Portal code cannot be changed</small>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Picture Section 
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-camera-fill me-2"></i>Profile Picture</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Upload New Picture</label>
                                <input type="file" class="form-control" name="picture" id="pictureInput" accept=".jpg,.jpeg,.png,.gif">
                                <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="keep_picture" id="keep_picture">
                                    <label class="form-check-label" for="keep_picture">
                                        Keep current picture
                                    </label>
                                </div>
                            </div>
                        </div>
                        <script>
                            const profileImage = document.querySelector("input#pictureInput");
                            const checkbox = document.querySelector("#keep_picture");
                            checkbox.addEventListener("change", function() {
                                if (checkbox.checked) {
                                    profileImage.setAttribute("disabled", true);
                                    profileImage.value = "";
                                } else {
                                    profileImage.removeAttribute("disabled");
                                }
                            });
                        </script>
                    </div>-->

                    <!-- Role & Status Section -->
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-briefcase-fill me-2"></i>Role & Status</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Staff Role <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                    <select class="form-select" name="staff_role" required>
                                        <option value="Teacher" <?php echo (isset($specificStaff['staff_role']) && $specificStaff['staff_role'] === 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                                        <option value="Admin" <?php echo (isset($specificStaff['staff_role']) && $specificStaff['staff_role'] === 'Admin') ? 'selected' : ''; ?>>Admin Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employment Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-activity"></i></span>
                                    <select class="form-select" name="staff_status">
                                        <option value="Active" <?php echo (isset($specificStaff['staff_status']) && $specificStaff['staff_status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Pending" <?php echo (isset($specificStaff['staff_status']) && $specificStaff['staff_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Inactive" <?php echo (isset($specificStaff['staff_status']) && $specificStaff['staff_status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Verification</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($specificStaff['account_verification'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Section -->
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-lock-fill me-2"></i>Change Password</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Leave blank to keep current password">
                                </div>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Confirm New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Repeat new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="text-muted">
                            <small><i class="bi bi-info-circle me-1"></i>Fields marked with <span class="text-danger">*</span> are required</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="staff_management.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-grad">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
        <script>
            // avatar preview
            document.getElementById('pictureInput')?.addEventListener('change', function(e) {
                const file = this.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowed.includes(file.type)) {
                    toastr.error('Only JPG and PNG images are allowed for profile photo');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const img = document.getElementById('avatarPreview');
                    if (img) img.src = ev.target.result;
                }
                reader.readAsDataURL(file);
            });

            // simple client-side validation
            document.getElementById('editStaffForm')?.addEventListener('submit', function(e) {
                const pwd = document.getElementById('new_password')?.value || '';
                const cpwd = document.getElementById('confirm_password')?.value || '';
                if (pwd || cpwd) {
                    if (pwd.length < 6) {
                        e.preventDefault();
                        toastr.error('Password must be at least 6 characters long');
                        return false;
                    }
                    if (pwd !== cpwd) {
                        e.preventDefault();
                        toastr.error('Passwords do not match');
                        return false;
                    }
                }
            });
        </script>
    <?php
    }
    ?>
</body>

</html>