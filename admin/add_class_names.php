<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentsFromSheet = json_decode(file_get_contents("php://input"), true);
    $allData = $studentsFromSheet["data"];
    foreach ($allData as $data) {
        if ($admin->addClass($pdo, $data["class"])) {
            echo "Done";
        } else {
            echo "Not done";
        }
    }
}
