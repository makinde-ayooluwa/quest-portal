<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";


// // Security check
// if (!in_array($adminData['staff_role'], ['super admin', 'head admin'])) {
//     header('Location: index.php');
//     exit();
// }

// Handle messages
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Get data
$roles = $admin->getAllRoles($pdo);
$featuresData = $admin->getFeaturesAndFolks($pdo);

// Get selected role permissions if editing
$rolePermissions = null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Management - Quest Portal</title>
    <?php include "head.php" ?>
    <style>
        .role-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .permissions-table {
            font-size: 0.9rem;
        }

        .permissions-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .permissions-table td {
            vertical-align: middle;
        }

        .tab-content {
            padding-top: 1.5rem;
        }

        .role-badge {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
        }

        .select-all {
            cursor: pointer;
            font-weight: 500;
            color: #0d6efd;
        }

        @media (max-width: 768px) {
            .permissions-table {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">

                <!-- Messages -->
                <?php if ($message): ?>
                    <script>
                        <?php
                        switch ($messageType) {
                            case "success":
                        ?>
                                toastr.success("<?= htmlspecialchars($message) ?>", "Success!")
                            <?php
                                break;
                            case "danger":
                            ?>
                                toastr.error("<?= htmlspecialchars($message) ?>", "Error!")
                        <?php
                                break;
                        }
                        ?>
                    </script>
                <?php endif; ?>

                <div class="role-card card-modern">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="bi bi-person-workspace me-2"></i>Role Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                            <i class="bi bi-plus-lg me-1"></i>New Role
                        </button>
                    </div>

                    <!-- Roles List -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role):
                                    if ($role['name'] !== 'head admin') {
                                ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary role-badge"><?= htmlspecialchars($role['name']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($role['description'] ?: 'No description') ?></td>
                                            <td><?= date('M j, Y', strtotime($role['created_at'])) ?></td>
                                            <td>
                                                <a href="role_permission.php?edit_role=<?= $role['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="bi bi-pencil"></i> Permissions
                                                </a>
                                                <?php if ($role['name'] !== 'head admin'): ?>
                                                    <form method="POST" action="role_management_handler.php" style="display: inline;" onsubmit="return confirm('Delete this role? All permissions will be removed.');">
                                                        <input type="hidden" name="action" value="delete_role">
                                                        <input type="hidden" name="role_id" value="<?= $role['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                endforeach; ?>
                                <?php if (empty($roles)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No roles found. Create your first role!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="role_management_handler.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_role">
                        <div class="mb-3">
                            <label class="form-label">Role Name *</label>
                            <input type="text" class="form-control" name="role_name" required minlength="3" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" maxlength="255"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script>
        // Select all checkboxes functionality
        document.querySelectorAll('.permissions-table tbody').forEach(table => {
            table.addEventListener('change', function(e) {
                if (e.target.type === 'checkbox') {
                    const checkboxes = table.querySelectorAll('input[type="checkbox"]');
                    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                    const totalCount = checkboxes.length;

                    // Update labels
                    checkboxes.forEach(cb => {
                        const label = cb.nextElementSibling;
                        if (cb.checked) {
                            label.textContent = '✅ Granted';
                            label.classList.add('text-success');
                            label.classList.remove('text-danger');
                        } else {
                            label.textContent = '❌ Denied';
                            label.classList.add('text-danger');
                            label.classList.remove('text-success');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>