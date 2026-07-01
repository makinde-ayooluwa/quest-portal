<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

// Validate id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    // Invalid id, redirect back to students list
    header('Location: students.php');
    exit();
}

// Fetch student
try {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('View student DB error: ' . $e->getMessage());
    $student = false;
}

if (!$student) {
    $_SESSION['error'] = 'Student not found.';
    header('Location: students.php');
    exit();
}

// Helper to safely echo
function e($v) { return htmlspecialchars($v ?? ''); }

// Resolve class id (if classes are managed in classes_names_only table)
$class_id = null;
try {
    $className = isset($student['class']) ? $student['class'] : '';
    if (!empty($className)) {
        $cstmt = $pdo->prepare('SELECT id FROM classes_names_only WHERE class_name = :class_name LIMIT 1');
        $cstmt->bindValue(':class_name', $className);
        $cstmt->execute();
        $classRow = $cstmt->fetch(PDO::FETCH_ASSOC);
        if ($classRow && isset($classRow['id'])) {
            $class_id = (int)$classRow['id'];
        }
    }
} catch (PDOException $e) {
    error_log('Class lookup error: ' . $e->getMessage());
    $class_id = null;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php"; ?>
    <title>View Student - <?php echo e($student['fullname']); ?></title>
    <style>
        .profile-card { max-width: 900px; margin: 2.5rem auto; background:#fff; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.06); padding:1.25rem;  }
        .profile-photo { width:120px; height:120px; object-fit:cover; border-radius:8px; border:3px solid #f1f5ff }
        .kv { font-weight:600; color:var(--sky-500) }
        .kvv { color:var(--rose-500) }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include 'header_sidebar.php'; ?>

    <div class="container-fluid">
        <div class="profile-card card-modern">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0 ms-3">Student Profile</h4>
                <div>
                    <a href="students.php" class="btn btn-sm btn-outline-secondary">&larr; Back</a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <img src="<?php echo e('../' . $student['picture']); ?>" alt="<?php echo e($student['fullname']); ?>" class="profile-photo mb-3">
                    <h5 class="mb-0"><?php echo e($student['fullname']); ?></h5>
                    <div class="text-muted small">Admission #: <?php echo e($student['admission_number']); ?></div>
                </div>
                <div class="col-md-8">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="kv">Class</div>
                            <div class="kvv d-flex align-items-center">
                                <span class="me-2"><?php echo e($student['class']); ?></span>
                            </div>
                        </div>
                        <div class="col-sm-6"><div class="kv">Email</div><div class="kvv"><?php echo e($student['email']); ?></div></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6"><div class="kv">Phone</div><div class="kvv"><?php echo e($student['phone']); ?></div></div>
                        <div class="col-sm-6"><div class="kv">Date of Birth</div><div class="kvv"><?php echo e($student['dob']); ?></div></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12"><div class="kv">Home Address</div><div class="kvv"><?php echo e($student['home_address']); ?></div></div>
                    </div>

                    <hr>
                    <h6>Parents / Guardians</h6>
                    <div class="row mb-2">
                        <div class="col-sm-6"><div class="kv">Father</div><div class="kvv"><?php echo e($student['father_name']); ?> &mdash; <?php echo e($student['father_phone']); ?> (<?php echo e($student['father_email']); ?>)</div></div>
                        <div class="col-sm-6"><div class="kv">Mother</div><div class="kvv"><?php echo e($student['mother_name']); ?> &mdash; <?php echo e($student['mother_phone']); ?> (<?php echo e($student['mother_email']); ?>)</div></div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-sm-6"><div class="kv">Admission Date</div><div class="kvv"><?php echo e($student['admission_date']); ?></div></div>
                        <div class="col-sm-6"><div class="kv">Account Verification</div><div class="kvv"><?php echo e($student['account_verification']); ?></div></div>
                    </div>
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
                    <p class="text-danger">Are you sure you want to delete <strong><?php echo e($student['fullname']); ?></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <a href="delete_student.php?id=<?php echo e($student['id']); ?>" class="btn btn-danger btn-sm">Yes, delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Promote Modal (single student) -->
    <div class="modal fade" id="promoteModal" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <form id="promoteForm" method="post" action="bulk_student_actions.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="promoteModalLabel">Promote / Demote student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Promote / Demote <strong><?php echo e($student['fullname']); ?></strong> to:</p>
                        <div class="mb-3">
                            <select name="promote_to" id="promoteSelectSingle" class="form-select">
                                <?php
                                if (!empty($classes) && is_array($classes)) {
                                    foreach ($classes as $c) {
                                        $name = isset($c['class_name']) ? $c['class_name'] : (is_string($c) ? $c : '');
                                        echo '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">(no classes available)</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="promote">
                        <input type="hidden" name="selected_ids[]" value="<?php echo e($student['id']); ?>">
                    </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Promote / Demote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
</body>

</html>
