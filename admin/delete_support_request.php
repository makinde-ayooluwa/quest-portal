<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
if(isset($_GET["id"])){
    if($admin->deleteSupportRequest($pdo,$_GET["id"])){
        $_SESSION["success"] = "Support request deleted successfully";
    }else{
        $_SESSION["error"] = "Error occured, Try again";
    }
}
header("Location: support_requests.php");