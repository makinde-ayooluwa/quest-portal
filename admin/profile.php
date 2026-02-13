<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php"; // sets $adminData and other dashboard variables
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php"; ?>
    <title>Admin Profile - Quest Schools Admin</title>
    <style>
        * {
            font-family: Montserrat;
        }

        .profile-card {
            max-width: 800px;
            margin: 2.5rem auto;
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #f1f1f1;
        }

        .stat {
            font-weight: 700;
            font-size: 1.1rem
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php"; ?>

    <div class="container-fluid">

        <div class="profile-card">
            <?php if (isset($_SESSION['error'])) {
            ?>
                <script>
                    toastr.error("<?php echo $_SESSION['error'] ?>", "Error!");
                </script>
            <?php
                unset($_SESSION['error']);
            } ?>
            <?php if (isset($_SESSION['success'])) { ?>
                <script>
                    toastr.success("<?php echo $_SESSION['success'] ?>", "Success!");
                </script>
            <?php
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['info'])) { ?>
                <script>
                    toastr.info("<?php echo $_SESSION['info'] ?>", "Info");
                </script>
            <?php
                unset($_SESSION['info']);
            }
            ?>
            <div class="d-flex gap-4 align-items-center">
                <div>
                    <img src="<?php echo htmlspecialchars($adminData['picture'] ?? 'assets/images/quest.jpg'); ?>" alt="avatar" class="profile-avatar">
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-1"><?php echo htmlspecialchars($adminData['fullname'] ?? 'Administrator'); ?></h3>
                    <p class="mb-1"><span class="badge bg-primary"><?php echo strtoupper($adminData['staff_role'] ?? 'Admin'); ?></span></p>
                    <div class="mt-3">
                        <a href="#editProfileModal" data-bs-toggle="modal" class="btn btn-outline-primary me-1"><i class="bi bi-pencil"></i> Edit Profile</a>
                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="bi bi-key"></i> Change Password</button>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <h5>Contact</h5>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($adminData['phone'] ?? 'Not set'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($adminData['email'] ?? ''); ?></p>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <h6>Recent Activities</h6>
                    <ul class="list-group">
                        <?php if (!empty($recentActivities)) {
                            foreach (array_slice($recentActivities, 0, 5) as $act) { ?>
                                <li class="list-group-item py-2 small"><?php echo htmlspecialchars($act['action'] ?? '') ?> <span class="text-muted">• <?php echo htmlspecialchars($act['ip_address'] ?? '') ?></span></li>
                            <?php }
                        } else { ?>
                            <li class="list-group-item py-2 small text-muted">No recent activity</li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Upcoming Events</h6>
                    <ul class="list-group">
                        <?php if (!empty($upcomingEvents)) {
                            foreach (array_slice($upcomingEvents, 0, 5) as $ev) { ?>
                                <li class="list-group-item py-2 small"><?php echo htmlspecialchars($ev['title'] ?? '') ?> <div class="text-muted small"><?php echo htmlspecialchars($ev['event_date'] ?? '') ?></div>
                                </li>
                            <?php }
                        } else { ?>
                            <li class="list-group-item py-2 small text-muted">No upcoming events</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="profile_update_handler.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Full name</label>
                                    <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($adminData['fullname'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($adminData['phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Profile picture (optional)</label>
                                <input type="file" name="picture" class="form-control" accept="image/*">
                                <div class="mt-3 text-center">
                                    <img src="<?php echo htmlspecialchars($adminData['picture'] ?? 'assets/images/quest.jpg'); ?>" alt="preview" class="rounded" style="max-width:100px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="change_password_handler.php" id="change_password_form" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" id="oldPwd" name="old_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" id="newPwd" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" id="confirmPwd" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
</body>

</html>