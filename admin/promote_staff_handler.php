<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staff_management.php');
    exit();
}

$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;

if ($staff_id <= 0) {
    $_SESSION['error'] = 'Invalid staff ID.';
    header('Location: staff_management.php');
    exit();
}

try {
    $role = $_POST["role"] ?? "";
    // First, verify the staff exists and is a Teacher
    $stmt = $pdo->prepare('SELECT fullname, email, staff_role FROM staffs WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $staff_id, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$staff) {
        $_SESSION['error'] = 'Staff not found.';
        header('Location: staff_management.php');
        exit();
    }

    // Update the staff role to Admin
    $updateStmt = $pdo->prepare('UPDATE staffs SET staff_role = :role WHERE id = :id');
    $updateStmt->bindValue(':role', $role, PDO::PARAM_STR);
    $updateStmt->bindValue(':id', $staff_id, PDO::PARAM_INT);
    $updateStmt->execute();

    // Send promotion email
    require_once 'admin_includes/email_utils.php';
    $emailUtils = new EmailUtils($host);
    $emailSent = $emailUtils->sendPromotionEmail($staff['email'], $staff['fullname']);

    if (!$emailSent) {
        error_log('Failed to send email to ' . $staff['email']);
        // Note: We don't set an error session here as the promotion was successful
    }

    $_SESSION['success'] = $staff['fullname'] . ' role has been changed to ' . strtoupper($role) . ".";
    header('Location: view_staff.php?id=' . $staff_id);
    exit();

} catch (PDOException $e) {
    error_log('Promote staff DB error: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while changing staff role.';
    header('Location: staff_management.php');
    exit();
}
?>
