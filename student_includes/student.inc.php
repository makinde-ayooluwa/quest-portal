<?php

// List pages that should NOT be redirected
$excluded_pages = ['setup.php','setup_handler.php','forgot_password.php','forgot_password_handler.php','reset_password.php','reset_password_handler.php'];

$current_page = basename($_SERVER['PHP_SELF']);

// Skip check for excluded pages
if (!in_array($current_page, $excluded_pages)) {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

$query = "SELECT * FROM students WHERE email = :email OR fullname = :email";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":email", $_SESSION["user"]);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$student = new Student($_SESSION['user']);
$studentData = $student->getStudent($pdo, $_SESSION['user']);

if(!$studentData){
header('Location: login.php');
}