<?php

session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";

if (isset($_POST["submit"])) {
    $email = $studentData["email"];
    $oldPwd = $_POST["oldPwd"];
    $newPwd = $_POST["newPwd"];
    $confirmPwd = $_POST["confirmPwd"];

    $data = [
        "email" => $email,
        "oldPwd" => $oldPwd,
        "newPwd" => $newPwd,
        "confirmPwd" => $confirmPwd,
        "hashedPwd" => password_hash($newPwd, PASSWORD_BCRYPT, ["cost" => 10])
    ];

    if ($student->changePassword($pdo, $data)) {
        unset($_SESSION["error"]);
        $_SESSION["success"] = "Password changed successfully";
    } else {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Error occured while changing password. Check if your old password is correct and other inputs.";
    }
}else{
    $_SESSION["error"] = "Page blocked";
}
header("Location: ./");
