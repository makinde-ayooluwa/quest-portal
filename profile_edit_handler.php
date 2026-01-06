<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$pwd = $_POST['password'];
$home_address = $_POST['home_address'];
$father_name = $_POST['father_name'];
$father_phone = $_POST['father_phone'];
$father_email = $_POST['father_email'];
$mother_name = $_POST['mother_name'];
$mother_phone = $_POST['mother_phone'];
$mother_email = $_POST['mother_email'];


$data = [
    'id' => $studentData["id"],
    'fullname' => $fullname,
    'email' => $email,
    'gender' => $gender,
    'phone' => $phone,
    'pwd' => $pwd,
    'hashedPwd' => password_hash($pwd, PASSWORD_BCRYPT, ["cost" => 10]),
    'home_address' => $home_address,
    'father_name' => $father_name,
    'father_phone' => $father_phone,
    'father_email' => $father_email,
    'mother_name' => $mother_name,
    'mother_phone' => $mother_phone,
    'mother_email' => $mother_email
];

if (empty($fullname) || empty($email) || empty($gender) || empty($phone) || empty($home_address) || empty($father_name) || empty($father_phone) || empty($father_email) || empty($mother_name) || empty($mother_phone) || empty($mother_email)) {
    $_SESSION['error'] = "All fields are required except profile picture which is labelled optional.";
    header("Location: profile_edit.php");
    exit();
} else {
    // Process the form data
    if ($student->updateStudent($pdo, $data)) {
        unset($_SESSION['error']);
        $_SESSION['success'] = "Profile updated successfully.";
        header("Location: profile_edit.php");
        exit();
    } else {
        unset($_SESSION['success']);
        $_SESSION['error'] = "Error occured while updating. Try again.";
        header("Location: profile_edit.php");
        exit();
    }
}
