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
    <div class="main container-fluid">
        <div class="row">
            <!-- Space Left -->
            <div class="col-md-3"></div>
            <!-- Main Content -->
            <div class="col-md-9">
                <style>
                    .uploadCard {
                        border-radius: 14px;
                        box-shadow: 0 1px 1px rgb(0, 0, 0, 0.2)
                    }
                </style>
                <div class="uploadCard my-5">
                    <div class="container">
                        <h1 class="text-start text-primary h3">Upload Results</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>