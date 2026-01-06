<?php

session_start();
include_once 'student_includes/autoloader.inc.php';
include_once 'student_includes/db.inc.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    //$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $login = new Login($username, //$hashedPassword 
    $password);

    if(empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: login.php");
        exit();
    }else{
        if($login->validate($pdo,$email,//$hashedPassword
        $password)){
            unset($_SESSION['error']);
            $_SESSION['user'] = $email;
            header("Location: index.php");
            exit();
        }else{
            unset($_SESSION['user']);
            $_SESSION["error"] = "Account not found or invalid login credentials. Try again.";
            header("Location: login.php");
            exit();
        }
    }
}