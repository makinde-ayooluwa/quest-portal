<?php
/*
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'];
    $mentor_name = $_POST['mentor_name'];
    $class_status = $_POST['class_status'];

    function mentor_has_class($pdo, $mentor_email) {
        $query = "SELECT * FROM classes WHERE mentor_name = :mentor_email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':mentor_email', $mentor_email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if(mentor_has_class($pdo, $mentor_name)) {
        $_SESSION['error'] = "This mentor is already assigned to a class.";
        header("Location: add_class.php");
        exit();
    }else{
        $admin->addClass($pdo, $class_name, $mentor_name,  $class_status);
    }
}
    unset($_SESSION['error']);
    $_SESSION['success'] = "Class added successfully.";
    header("Location: add_class.php");
    exit();
    */