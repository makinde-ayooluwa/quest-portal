<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
$id = isset($_GET["id"]) ? $_GET["id"] : null;
function getId($pdo, $id)
{
    $query = "SELECT * FROM staffs WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff/Mentor - Quest Schools Admin</title>
    <!-- Bootstrap CSS -->
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/Quest logo.jpg" type="image/x-icon">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=fire">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">-->
    <!--Styles-->
    <link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!--Scripts-->
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .edit-card {
            max-width: 700px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .btn-grad {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, #198754 60%, #0d6efd 100%);
            color: #fff;
        }
    </style>
</head>

<body><?php
        if (!isset($_GET["id"])) {
        ?>
        <style>
            .error-container {
                max-width: 500px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff3f3;
                border: 1px solid #ffcccc;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .error-container h2 {
                color: #d9534f;
                margin-bottom: 1rem;
            }

            .error-container p {
                color: #555;
                margin-bottom: 1.5rem;
            }

            .error-container a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #d9534f;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #c9302c;
            }
        </style>
        <div class="error-container">
            <h2>Error</h2>
            <p>Sorry, the method used to request this page is not allowed.</p>
            <a href="./">Go to Homepage</a>
        </div>
    <?php
        } else if (!getId($pdo, $id)) {
    ?>
        <style>
            .error-container {
                max-width: 500px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff3f3;
                border: 1px solid #ffcccc;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .error-container h2 {
                color: #d9534f;
                margin-bottom: 1rem;
            }

            .error-container p {
                color: #555;
                margin-bottom: 1.5rem;
            }

            .error-container a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #d9534f;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #c9302c;
            }
        </style>
        <div class="error-container">
            <h2>Error</h2>
            <p>Sorry, the method used to request this page is not allowed.</p>
            <a href="./">Go to Homepage</a>
        </div>
    <?php
        } else {
            $_SESSION["staff_id"] = $id;
            $admin->deleteStaff($pdo, $id);
            $_SESSION["success"] = "Staff deleted successfully.";
            header("Location: staff_management.php");
            exit();
        }
    ?>
</body>

</html>