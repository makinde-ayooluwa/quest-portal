<?php

session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";


if (isset($_POST["submit"])) {
    // Secure file upload: validate extension and size
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $fileExt = strtolower(pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedExt)) {
        $_SESSION["error"] = "Invalid image type. Only JPG, JPEG, PNG, WEBP, GIF allowed.";
        header("Location: ./");
        exit();
    }
    if ($_FILES["picture"]["size"] > 3 * 1024 * 1024) {
        $_SESSION["error"] = "Image too large. Maximum 3MB allowed.";
        header("Location: ./");
        exit();
    }

    $target_dir = "assets/images/";
    $newFileName = 'profile_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;
    $target_file = $target_dir . $newFileName;

    $data = [
        "email" => $studentData["email"],
        "picture" => $_FILES["picture"],
        "fullPath" => $target_file,
        "pwd" => $_POST["pwd"]
    ];

    if ($student->changePicture($pdo, $data)) {
        unset($_SESSION["error"]);
        $_SESSION["success"] = "Profile picture changed successfully";
        move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    } else {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Error occured while changing picture. Check if your password is correct.";
    }
}else{
    $_SESSION["error"] = "Page blocked";
}
header("Location: ./");
