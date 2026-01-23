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
    header("Location: manage_results.php");
    exit();
}

/* Validate inputs */
$resultId = trim($_POST["result_id"] ?? '');
$academicTerm = trim($_POST["academic_term"] ?? '');
$resultFile = trim($_POST["result_file"] ?? '');

if (empty($resultId) || empty($academicTerm) || empty($resultFile)) {
    $_SESSION["error"] = "All fields are required";
    header("Location: manage_results.php");
    exit();
}

/* Validate URL */
if (!filter_var($resultFile, FILTER_VALIDATE_URL)) {
    $_SESSION["error"] = "Please provide a valid URL";
    header("Location: manage_results.php");
    exit();
}

$admin = new Admin($_SESSION["admin"]);
$data = [
    "academic_term" => $academicTerm,
    "result_file" => $resultFile
];

if ($admin->updateResult($pdo, $resultId, $data)) {
    $_SESSION["success"] = "Result updated successfully";
} else {
    $_SESSION["error"] = "Error occurred, try again";
}

header("Location: manage_results.php");
exit();
