<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

// Validate id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    // Invalid id, redirect back to staff list
    header('Location: staff_management.php');
    exit();
}

// Fetch staff
try {
    $stmt = $pdo->prepare('SELECT * FROM staffs WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('View staff DB error: ' . $e->getMessage());
    $staff = false;
}

if (!$staff) {
    $_SESSION['error'] = 'Staff not found.';
    header('Location: staff_management.php');
    exit();
}

// Helper to safely echo
function e($v)
{
    return htmlspecialchars($v ?? '');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php"; ?>
    <title>View Staff - <?php echo e($staff['fullname']); ?></title>
    <style>
        .profile-card {
            max-width: 900px;
            margin: 2.5rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            padding: 1.25rem
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #f1f5ff
        }

        .kv {
            font-weight: 600;
            color: #333
        }

        .kvv {
            color: #555
        }
    </style>
</head>

<body>
    <?php include 'header_sidebar.php'; ?>

    <?php
    if (isset($_SESSION["error"])) {
    ?>
        <script>
            toastr.error("<?php echo $_SESSION['error'] ?>", "Error!");
        </script>
    <?php
        unset($_SESSION["error"]);
    }

    if (isset($_SESSION["success"])) {
    ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
        </script>
    <?php
        unset($_SESSION["success"]);
    }
    ?>

    <div class="container-fluid">
        <div class="profile-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">Staff Profile</h4>
                <div>
                    <a href="staff_management.php" class="btn btn-sm btn-outline-secondary">&larr; Back</a>
                    <!-- <a href="edit_staff.php?id=<?php //echo e($staff['id']); 
                                                    ?>" class="btn btn-sm btn-primary ms-2">Edit</a> -->
                    <?php
                    if($staff["staff_role"] == "head admin"){
                        ?>
                        
                        <?php
                    }
                    elseif ($adminData["staff_role"] == "head admin") {
                        if ($staff['staff_role'] !== 'admin') { ?>
                            <button type="button" class="btn btn-sm btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#promoteModal">Promote to Admin</button>
                        <?php } ?>
                        <button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <img src="<?php echo e($staff["picture"]); ?>" alt="<?php echo e($staff['fullname']); ?>" class="profile-photo mb-3">
                    <h5 class="mb-0"><?php echo e($staff['fullname']); ?></h5>
                    <div class="text-muted small">Portal Code: <?php echo e($staff['portal_code']); ?></div>
                </div>
                <div class="col-md-8">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="kv">Role</div>
                            <div class="kvv"><?php echo strtoupper(e($staff['staff_role'])); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="kv">Email</div>
                            <div class="kvv"><?php echo e($staff['email']); ?></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="kv">Phone</div>
                            <div class="kvv"><?php echo e($staff['phone']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="kv">Gender</div>
                            <div class="kvv"><?php echo e($staff['gender']); ?></div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="kv">Status</div>
                            <div class="kvv"><?php echo e($staff['staff_status']); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="kv">Account Verification</div>
                            <div class="kvv"><?php echo e($staff['account_verification']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Promote Modal -->
    <div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoteModalLabel">Confirm Promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to promote <strong><?php echo e($staff['fullname']); ?></strong> from Teacher to Admin?</p>
                    <p class="text-muted small">This will change their role and may affect their access permissions.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" action="promote_staff_handler.php" style="display: inline;">
                        <input type="hidden" name="staff_id" value="<?php echo e($staff['id']); ?>">
                        <button type="submit" class="btn btn-warning btn-sm">Yes, promote</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Are you sure you want to delete <strong><?php echo e($staff['fullname']); ?></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <a href="delete_staff.php?id=<?php echo e($staff['id']); ?>" class="btn btn-danger btn-sm">Yes, delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
</body>

</html>