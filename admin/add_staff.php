<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

// Capture flash messages
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
        :root {
            --card-max-width: 920px;
        }

        * {
            font-family: Montserrat, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }

        body {
            background: #f8f9fa;
        }

        .add-card {
            max-width: var(--card-max-width);
            margin: 3rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

        /* Make form controls full-width and responsive */
        .add-card .form-control,
        .add-card .form-select {
            width: 100%;
        }

        .form-section-title {
            margin-top: .25rem;
            margin-bottom: .75rem;
            font-weight: 600;
        }

        .btn-grad {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
        }

        .btn-grad:hover {
            filter: brightness(0.95);
        }

        /* Prevent accidental horizontal overflow from long labels or inputs */
        html,
        body {
            overflow-x: hidden;
        }

        @media (max-width: 576px) {
            .add-card {
                margin: 1rem;
                padding: 1rem;
            }

            :root {
                --card-max-width: 100%;
            }

            .d-flex.gap-2 {
                flex-direction: column-reverse;
            }
        }

        /* Page-local header fix: make header fixed on this page so it sticks reliably */
        header.position-sticky {
            position: fixed !important;
            top: 0 !important;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        /* push main content below header to avoid overlap (header ~56px tall) */
        .main-content {
            padding-top: 72px;
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php" ?>

    <main class="main-content">
        <div class="container">
            <div class="add-card" role="region" aria-labelledby="addStaffHeading">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h2 id="addStaffHeading" class="mb-1"><i class="bi bi-person-plus me-2"></i>Add Staff / Mentor</h2>
                        <small class="text-muted">Create staff accounts and assign roles</small>
                    </div>
                </div>

                <!-- Flash messages queued and displayed after toastr loads -->
                <div id="flash-data" data-error="<?php echo htmlspecialchars($flashError, ENT_QUOTES); ?>" data-success="<?php echo htmlspecialchars($flashSuccess, ENT_QUOTES); ?>"></div>

                <form id="addStaffForm" action="add_staff_handler.php" method="post" enctype="multipart/form-data">
                    <h5 class="form-section-title">Personal Details</h5>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="fullname" required placeholder="e.g. Jane Doe" aria-required="true">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required placeholder="name@example.com" aria-required="true">
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" required placeholder="e.g. +1234567890">
                        </div>
                    </div>

                    <h5 class="form-section-title">Role & Academic Details</h5>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">Select</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <!-- Assigned Class removed per request -->
                    </div>

                    <h5 class="form-section-title">Employment & Status</h5>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Employment Date</label>
                            <input type="date" class="form-control" name="employment_date" required>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="staff_status" required>
                                <option value="">Select</option>
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="form-section-title">Account / Dashboard</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Portal Code</label>
                            <input type="text" class="form-control" name="portal_code" required>
                        </div>
                    </div>

                    <!-- Hidden fields expected by handler: assigned_class and subjects -->
                    <input type="hidden" name="assigned_class" value="None">
                    <input type="hidden" name="subjects" value="None">

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="staff_management.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Add Staff</button>
                    </div>
                </form>
            </div>
        </div>

        <?php include "footer.php" ?>
    </main>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        // Show queued flash messages after toastr is available
        (function() {
            const flashNode = document.getElementById('flash-data');
            if (flashNode) {
                const err = flashNode.dataset.error || '';
                const ok = flashNode.dataset.success || '';
                if (err) toastr.error(err, 'Error');
                if (ok) toastr.success(ok, 'Success');
            }

            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        })();

        // Basic client-side validation before submit
        document.getElementById('addStaffForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value.trim();
            const fullname = this.querySelector('input[name="fullname"]').value.trim();
            const role = this.querySelector('select[name="role"]').value;
            const phone = this.querySelector('input[name="phone"]').value.trim();
            const gender = this.querySelector('select[name="gender"]').value;
            const employment_date = this.querySelector('input[name="employment_date"]').value;
            const staff_status = this.querySelector('select[name="staff_status"]').value;
            const portal_code = this.querySelector('input[name="portal_code"]').value.trim();
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!fullname) {
                e.preventDefault();
                toastr.error('Full name is required.');
                return false;
            }
            if (!emailRe.test(email)) {
                e.preventDefault();
                toastr.error('Please enter a valid email address.');
                return false;
            }
            if (!role) {
                e.preventDefault();
                toastr.error('Please select a role.');
                return false;
            }
            if (!phone) {
                e.preventDefault();
                toastr.error('Phone is required.');
                return false;
            }
            if (!gender) {
                e.preventDefault();
                toastr.error('Gender is required.');
                return false;
            }
            if (!employment_date) {
                e.preventDefault();
                toastr.error('Employment date is required.');
                return false;
            }
            if (!staff_status) {
                e.preventDefault();
                toastr.error('Staff status is required.');
                return false;
            }
            if (!portal_code) {
                e.preventDefault();
                toastr.error('Portal code is required.');
                return false;
            }
            return true;
        });
    </script>
</body>

</html>