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

if (empty($resultId) || !is_numeric($resultId)) {
    $_SESSION["error"] = "Invalid result ID";
    header("Location: manage_results.php");
    exit();
}

$admin = new Admin($_SESSION["admin"]);

if ($admin->deleteResult($pdo, $resultId)) {
    $_SESSION["success"] = "Result deleted successfully";
} else {
    $_SESSION["error"] = "Error occurred while deleting result";
}

header("Location: manage_results.php");
exit();
