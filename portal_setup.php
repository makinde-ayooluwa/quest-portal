<?php

session_start();
include_once 'student_includes/autoloader.inc.php';
include_once 'student_includes/db.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $picture = $_FILES['picture'];
    $pwd = $_POST['password'];
    $confirm_pwd = $_POST['confirm_password'];
    $admission_number = isset($_GET["admission_number"]) ? $_GET["admission_number"] : $_POST["admission_number"];

    // Validate password confirmation
    if ($pwd !== $confirm_pwd) {
        $_SESSION["error"] = "Passwords do not match. Please try again.";
        header("Location: setup.php?admission_number=$admission_number");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, ["cost" => 10]);
    $target_dir = "assets/images/";
    $target_file = $target_dir . basename($_FILES["picture"]["name"]);
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);

    $data = [
        "admission_number" => $admission_number,
        "email" => $email,
        "dob" => $_POST["dob"],
        "gender" => $_POST["gender"],
        "picture" => $target_file,
        "phone" => $_POST["phone"],
        "home_address" => $_POST["home_address"],
        "mother_name" => $_POST["mother_name"],
        "mother_email" => $_POST["mother_email"],
        "mother_phone" => $_POST["mother_phone"],
        "father_name" => $_POST["father_name"],
        "father_email" => $_POST["father_email"],
        "father_phone" => $_POST["father_phone"],
        "pwd" => $pwd,
        "hashedPwd" => $hashedPwd,
    ];

    define("SETUP", new Setup($pdo, $data));

    if (!(empty($email) || empty($picture["name"]) || empty($pwd) || empty($admission_number))) {
        $_SESSION["success"] = "Account setup completed. Login to your portal now.";
        SETUP->setup($pdo, $data);
        header("Location: login.php");
    } else {
        $_SESSION["error"] = "All required fields must be filled.";
        header("Location: setup.php?admission_number=$admission_number");
    }
}
