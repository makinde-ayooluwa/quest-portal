<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $admission_number = $_POST['admission_number'];
    $class = $_POST['class'];

    $studentData = [
        'fullname' => $fullname,
        'email' => $email,
        'admission_number' => $admission_number,
        'class' => $class
    ];

    if (empty($fullname) || empty($email) || empty($admission_number) || empty($class)) {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Fill out all required fields";
        header("Location: add_student.php");
    } else {
        if (!$admin->addStudent($pdo, $studentData)) {
            unset($_SESSION["success"]);
            // $emailUtils = new EmailUtils($host);
            // $emailSent = $emailUtils->sendStudentSetupEmail(
            //     $studentData['email'],
            //     $studentData['fullname'],
            //     $studentData['admission_number']
            // );
            $_SESSION["error"] = "Error occured while adding student. Student may exist before";
            header("Location: add_student.php");
        } else {
            unset($_SESSION["error"]);
        }
    }
}
