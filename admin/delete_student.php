<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

function idExists($pdo, $id)
{
    $sql = "SELECT * FROM students WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
if (!isset($_GET["id"])) {
} else if (!idExists($pdo, $_GET["id"])) {
}else{
    $admin->deleteStudent($pdo, $_GET["id"]);
    $_SESSION["success"] = "Student removed successfully";
    header("Location: students.php");
    exit();
}
