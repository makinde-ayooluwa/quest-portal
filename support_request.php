<?php

session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

if(!isset($_POST["submit"])){
    ?>

    Page blocked
    <?php
}

$support_data = [
    "support_topic" => $_POST["topic"],
    "support_priority" => $_POST["priority"],
    "support_subject" => $_POST["subject"],
    "support_description" => $_POST["message"],
    "email" => $studentData["email"],
    "phone" => $_POST["phone"],
];

if($student->raiseSupport($pdo, $support_data)){
    $_SESSION["success"] = "Support request sent successfully";
}else{
    $_SESSION["error"] = "Error occured. Try checking your inputs and try again";
}

header("Location: support.php");