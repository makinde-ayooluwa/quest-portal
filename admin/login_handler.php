<?php


session_start();

include "admin_includes/autoloader.inc.php";
require_once "admin_includes/db.inc.php";

$email = $_POST["email"];
$password = $_POST["password"];

$login = new Login($email);

//$loginData = $login->getAdmin($pdo, $email, $password);

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "All input fields are required to complete login process.";
    header("Location: login.php");
    exit();
} else {
    if ($login->getAdmin($pdo, $email, $password)) {
        $_SESSION["admin"] = $email;
        unset($_SESSION["error"]);
        header("Location:./");
    } else {
        $_SESSION['error'] = "Account not found or incorrect credentials. Try Again.";
        header("Location: login.php");
        exit();
    }
}
