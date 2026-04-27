<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
$roles = $admin->getAllRoles($pdo);
$featuresData = $admin->getFeaturesAndFolks($pdo);

// Get selected role permissions if editing
$selectedRole = null;
$rolePermissions = null;

if (isset($_GET['edit_role'])) {
    foreach ($roles as $role) {
        if ($role['id'] == $_GET['edit_role']) {
            $selectedRole = $role;
            $rolePermissions = $admin->getRolePermissions($pdo, $role['name']);
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Role Permissions</title>
    <?php include "head.php" ?>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 main-content">
                <?php if ($selectedRole) { ?>
                    <!-- Permissions Editor -->
                    <div class="role-card">
                        <h4><i class="bi bi-shield-check me-2"></i>Permissions for <strong><?= htmlspecialchars($selectedRole['name']) ?></strong></h4>

                        <ul class="nav nav-tabs mb-3" id="permissionsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="features-tab" data-bs-toggle="tab" href="#features">Main Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sublinks-tab" data-bs-toggle="tab" href="#sublinks">Sublinks</a>
                            </li>
                        </ul>

                        <form method="POST" action="role_management_handler.php" id="permissionsForm">
                            <input type="hidden" name="action" value="update_permissions">
                            <input type="hidden" name="role_name" value="<?= htmlspecialchars($selectedRole['name']) ?>">

                            <div class="tab-content">
                                <!-- Features Tab -->
                                <div class="tab-pane fade show active" id="features" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table permissions-table">
                                            <thead>
                                                <tr>
                                                    <th>Feature</th>
                                                    <th>Access</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rolePermissions['features'] ?? [] as $feature): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($feature['title']) ?></td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="feature_permissions[]" value="<?= htmlspecialchars($feature['unique_id']) ?>"
                                                                    id="feature_<?= $feature['unique_id'] ?>" <?= $feature['has_access'] ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="feature_<?= $feature['unique_id'] ?>">
                                                                    <?= $feature['has_access'] ? '✅ Granted' : '❌ Denied' ?>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Sublinks Tab -->
                                <div class="tab-pane fade" id="sublinks" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table permissions-table">
                                            <thead>
                                                <tr>
                                                    <th width="60%">Sublink</th>
                                                    <th width="20%">Parent</th>
                                                    <th>Access</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rolePermissions['sublinks'] ?? [] as $sublink): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($sublink['title']) ?> <small class="text-muted">(<?= htmlspecialchars($sublink['link']) ?>)</small></td>
                                                        <td><?= htmlspecialchars($sublink['parent_unique_id']) ?></td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="sublink_permissions[]" value="<?= htmlspecialchars($sublink['unique_id']) ?>"
                                                                    id="sublink_<?= $sublink['unique_id'] ?>" <?= $sublink['has_access'] ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="sublink_<?= $sublink['unique_id'] ?>">
                                                                    <?= $sublink['has_access'] ? '✅ Granted' : '❌ Denied' ?>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i>Save Permissions
                                </button>
                                <a href="role_management.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php } else {
                ?>
                    Access blocked
                <?php
                }


                ?>
            </div>
        </div>
    </div>

</body>

</html>