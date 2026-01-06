<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$currentClass = $_SESSION["currentClass"];

$mentor_email = $_GET["mentor_email"];
$class_name = $_GET["class_name"];

if (!isset($mentor_name) && !isset($class_name)) {
} else {
    $query = "DELETE FROM classes WHERE mentor_email = :mentor_email AND class_name = :class_name;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":mentor_email", $mentor_email);
    $stmt->bindParam(":class_name", $class_name);
    if($stmt->execute()){
        $_SESSION["success"] = "Teacher successfully unassigned";
    }else{
        $_SESSION["error"] = "Error occured while unassigning teacher";
    }
    
    header("Location: edit_class.php?id=" . $currentClass);
}
