<?php

session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";


if (isset($_POST["submit"])) {
    $data = [
        "email" => $studentData["email"],
        "picture" => $_FILES["picture"],
        "fullPath" => "assets/images/" . $_FILES["picture"]["name"],
        "pwd" => $_POST["pwd"]
    ];

    if ($student->changePicture($pdo, $data)) {
        unset($_SESSION["error"]);
        $_SESSION["success"] = "Profile picture changed successfully";
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES["picture"]["name"]);
        move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    } else {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Error occured while changing picture. Check if your password is correct.";
    }
}else{
    $_SESSION["error"] = "Page blocked";
}
header("Location: ./");
