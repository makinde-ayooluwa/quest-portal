<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $portal_code = $_POST['portal_code'];
    $staff_role = $_POST['role'];
    $class = $_POST['assigned_class'] ?? 'None';
    $subject = $_POST['subjects'] ?? 'None';
    $employment_date = $_POST['employment_date'];
    $staff_status = 'active';

    // Validate required fields
    if (
        empty($fullname) || empty($email) || empty($phone) || empty($gender) ||
        empty($portal_code) || empty($staff_role) || empty($employment_date) || empty($staff_status)
    ) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add_staff.php");
        exit();
    }

    // Save to database
    if (!$admin->addStaff(
        $pdo,
        $fullname,
        $email,
        $phone,
        $gender,
        $portal_code,
        $staff_role,
        $employment_date,
        $staff_status
    )) {
        $_SESSION['error'] = "Error occured while adding staff. Try again.";
        header("Location: add_staff.php");
        exit();
    } else {
        // Send setup email to staff
        $emailUtils = new EmailUtils();
        $emailSent = $emailUtils->sendStaffSetupEmail(
            $email,
            $fullname,
            $portal_code,
            $staff_role
        );

        if ($emailSent) {
            $_SESSION['success'] = "Staff/Teacher added successfully and setup email sent.";
        } else {
            $_SESSION['success'] = "Staff/Teacher added successfully, but email could not be sent.";
        }
        header("Location: add_staff.php");
        exit();
    }
}
