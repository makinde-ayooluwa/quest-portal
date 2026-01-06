<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_SESSION['staff_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $picture = $_FILES['picture'];
    $phone = $_POST['phone'];
    $staff_role = $_POST['staff_role'];
    $staff_status = $_POST['staff_status'];

    if (empty($fullname) || empty($email) || empty($gender) || empty($phone) || empty($staff_role) || empty($staff_status)) {
        $_SESSION['error'] = "All fields except profile photo are required.";
        header("Location: edit_staff.php?id=" . $staff_id);
        exit();
    } else {
       
    }
} else {
    header("Location: ./");
}
