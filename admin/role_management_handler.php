<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";



// Only super admin and head admin can manage roles
if (!in_array($adminData['staff_role'], ['admin', 'head admin'])) {
    $_SESSION['error'] = 'Insufficient permissions';
    header('Location: index.php');
    exit();
}

$message = '';
$type = '';

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_role':
                if ($admin->createRole($pdo, $_POST['role_name'], $_POST['description'])) {
                    $message = 'Role created successfully';
                    $type = 'success';
                } else {
                    $message = 'Failed to create role';
                    $type = 'error';
                }
                break;
                
            case 'update_role':
                if ($admin->updateRole($pdo, $_POST['role_id'], $_POST['role_name'], $_POST['description'])) {
                    $message = 'Role updated successfully';
                    $type = 'success';
                } else {
                    $message = 'Failed to update role';
                    $type = 'error';
                }
                break;
                
            case 'delete_role':
                $role = $admin->getRole($pdo, $_POST['role_id']);
                if ($role && $admin->deleteRole($pdo, $_POST['role_id'])) {
                    $message = 'Role "' . $role['name'] . '" deleted successfully';
                    $type = 'success';
                } else {
                    $message = 'Failed to delete role';
                    $type = 'error';
                }
                break;
                
            case 'update_permissions':
                $featurePermissions = isset($_POST['feature_permissions']) ? $_POST['feature_permissions'] : [];
                $sublinkPermissions = isset($_POST['sublink_permissions']) ? $_POST['sublink_permissions'] : [];
                if ($admin->updateRolePermissions($pdo, $_POST['role_name'], $featurePermissions, $sublinkPermissions)) {
                    $message = 'Permissions updated successfully';
                    $type = 'success';
                } else {
                    $message = 'Failed to update permissions';
                    $type = 'error';
                }
                break;
        }
    }
}

$_SESSION['message'] = $message;
$_SESSION['message_type'] = $type;
header('Location: role_management.php');
exit();
?>

