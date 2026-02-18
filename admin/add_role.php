<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$data = json_decode(file_get_contents("php://input"), true);

$role_name = $data["role_name"];

if ($admin->addRole($pdo, $role_name)) {
    echo json_encode([
        "status" => "success",
        "message" => "Role added successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error occured"
    ]);
}
