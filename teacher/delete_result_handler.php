<?php
session_start();
require_once 'teacher_includes/autoloader.inc.php';
require_once 'teacher_includes/db.inc.php';
require_once 'teacher_includes/teacher.inc.php';

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['result_id']) || !is_numeric($_GET['result_id'])) {
    $_SESSION['error'] = 'Invalid result ID.';
    header("Location: manage_results.php");
    exit();
}

$resultId = (int)$_GET['result_id'];
$email = $_SESSION["teacher"];
$teacher = new Teacher($email);

if ($teacher->deleteResult($pdo, $resultId, $email)) {
    $_SESSION['success'] = 'Result deleted successfully.';
} else {
    $_SESSION['error'] = 'Failed to delete result or you do not have permission to delete this result.';
}

header("Location: manage_results.php");
exit();
?>
