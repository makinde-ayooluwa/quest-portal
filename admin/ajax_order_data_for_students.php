<?php
session_start();
header("Content-Type: application/json");

include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$external_data = json_decode(file_get_contents("php://input"),true);
$order = [
    "order" => $external_data['order'],
    "mode" => $external_data["mode"]
];

echo json_encode($admin->getStudentsInOrder($pdo, $order['order'],$order['mode']));