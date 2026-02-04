<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

echo json_encode([
    "verified" => count($admin->getVerifiedStaffs($pdo)),
    "unverified" => count($admin->getUnverifiedStaffs($pdo))
]);
