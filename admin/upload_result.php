<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Upload Result | Quest Portal</title>
</head>

<body>
    <?php include "header_sidebar.php" ?>
    <div class="main container">
        <div class="row">
            <!-- Space Left -->
            <div class="col-md-3"></div>
            <!-- Main Content -->
            <div class="col-md-9"></div>
        </div>
    </div>
</body>

</html>