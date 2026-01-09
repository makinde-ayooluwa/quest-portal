<?php

include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {
    case "POST":
        $class_name = $_POST["class_name"];
        if($admin->addClass($pdo, $class_name)){
            return true;
        }
        break;
}