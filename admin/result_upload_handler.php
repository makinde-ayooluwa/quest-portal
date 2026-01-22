<?php
session_start();

require "admin_includes/autoloader.inc.php";
require "admin_includes/db.inc.php";
require "admin_includes/admin.inc.php";

/* Auth check */
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

/* Only allow POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: upload_result.php");
    exit();
}

/* Validate inputs */
$academic_term = trim($_POST["academic_term"] ?? '');
$student_admission = trim($_POST["student_admission"] ?? '');
$result_file = trim($_POST["result_file"] ?? '');

if ($academic_term === '' || $student_admission === '' || $result_file === '') {
    $_SESSION["error"] = "All fields are required";
    header("Location: upload_result.php");
    exit();
}

$data = [
    "academic_term" => $academic_term,
    "student_admission_number" => $student_admission,
    "result_file" => $result_file
];

/* Upload result */
if ($admin->uploadResult($pdo, $data)) {
    $_SESSION["success"] = "Result uploaded successfully";
} else {
    $_SESSION["error"] = "Error occurred, try again";
}

header("Location: upload_result.php");
exit();
