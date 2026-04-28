<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$flashError = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
$flashSuccess = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Staff/Mentor - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        .main-content {
            margin-left: 220px; padding: 2rem 1rem;
        }
        .add-card {
            max-width: 920px;
            margin: 0 auto;
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            animation: fadeInUp 0.5s ease forwards;
            overflow: hidden;
            position: relative;
        }
        .add-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }
        .section-title-admin {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.125rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: var(--slate-800);
        }
        .section-title-admin::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--slate-200), transparent);
            margin-left: 0.5rem;
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
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; }
        }
        @media (max-width: 576px) {
            .add-card { padding: 1.25rem; }
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>

    <main class="main-content">
        <div class="add-card" role="region" aria-labelledby="addStaffHeading">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <h2 id="addStaffHeading" class="mb-1 fw-bold"><i class="bi bi-person-plus text-green me-2"></i>Add User</h2>
                    <small class="text-muted">Create user accounts and assign roles</small>
                </div>

            <div id="flash-data" data-error="<?php echo htmlspecialchars($flashError, ENT_QUOTES); ?>" data-success="<?php echo htmlspecialchars($flashSuccess, ENT_QUOTES); ?>"></div>

            <form id="addStaffForm" action="add_staff_handler.php" method="post" enctype="multipart/form-data">
                <div class="section-title-admin"><i class="bi bi-person text-green"></i>Personal Details</div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="fullname" required placeholder="e.g. Jane Doe">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required placeholder="name@example.com">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Gender</label>
                        <select class="form-select" name="gender" required>
                            <option value="">Select</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="tel" class="form-control" name="phone" required placeholder="e.g. +1234567890">
                    </div>

                <div class="section-title-admin"><i class="bi bi-briefcase text-green"></i>Role & Academic Details</div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select</option>
                            <?php
                            if (count($admin->getAllRoles($pdo)) < 1) {
                            ?>
                                <option value="" disabled>No roles available</option>
                            <?php
                            } else {
                                foreach ($admin->getAllRoles($pdo) as $role) {
                                    if ($role['name'] !== "head admin") {
                            ?>
                                        <option value="<?php echo $role['name'] ?>"><?php echo $role['name'] ?></option>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                <div class="section-title-admin"><i class="bi bi-calendar-event text-green"></i>Employment & Status</div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label fw-semibold">Employment Date</label>
                        <input type="date" class="form-control" name="employment_date" required>
                    </div>

                <div class="section-title-admin"><i class="bi bi-shield-lock text-green"></i>Account / Dashboard</div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Portal Code</label>
                        <input type="text" class="form-control" name="portal_code" required>
                    </div>

                <input type="hidden" name="assigned_class" value="None">
                <input type="hidden" name="subjects" value="None">

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="staff_management.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Cancel</a>
                    <button type="submit" class="btn btn-save"><i class="bi bi-save me-2"></i>Add User</button>
                </div>
            </form>
        </div>
    </main>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        (function() {
            const flashNode = document.getElementById('flash-data');
            if (flashNode) {
                const err = flashNode.dataset.error || '';
                const ok = flashNode.dataset.success || '';
                if (err) toastr.error(err, 'Error');
                if (ok) toastr.success(ok, 'Success');
            }
            document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
        })();

        document.getElementById('addStaffForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value.trim();
            const fullname = this.querySelector('input[name="fullname"]').value.trim();
            const role = this.querySelector('select[name="role"]').value;
            const phone = this.querySelector('input[name="phone"]').value.trim();
            const gender = this.querySelector('select[name="gender"]').value;
            const employment_date = this.querySelector('input[name="employment_date"]').value;
            const portal_code = this.querySelector('input[name="portal_code"]').value.trim();
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!fullname) { e.preventDefault(); toastr.error('Full name is required.'); return false; }
            if (!emailRe.test(email)) { e.preventDefault(); toastr.error('Please enter a valid email address.'); return false; }
            if (!role) { e.preventDefault(); toastr.error('Please select a role.'); return false; }
            if (!phone) { e.preventDefault(); toastr.error('Phone is required.'); return false; }
            if (!gender) { e.preventDefault(); toastr.error('Gender is required.'); return false; }
            if (!employment_date) { e.preventDefault(); toastr.error('Employment date is required.'); return false; }
            if (!portal_code) { e.preventDefault(); toastr.error('Portal code is required.'); return false; }
            return true;
        });
    </script>
</body>

</html>
