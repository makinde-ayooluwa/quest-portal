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
