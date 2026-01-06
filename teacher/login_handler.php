<?php

session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";

$login = new TeacherLogin();

if (isset($_POST["submit"])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($login->authenticate($pdo, $email, $password)) {
        unset($_SESSION['error']);
        $_SESSION['teacher'] = $email;
        header("Location: ./");
        exit();
    } else {
        unset($_SESSION['teacher']);
        $_SESSION["error"] = "Login failed. Check your inputs and try again.";
        header("Location: login.php");
        exit();
    }
} else {
?>
    <?php
}
