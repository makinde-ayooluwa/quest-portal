<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["fullname"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $class = $_POST["class"];
    //$scholarship_status = $_POST["scholarship"];

    if(empty($fullname) || empty($gender) || empty($email) || empty($class) /*|| empty($scholarship_status)*/) {
        $_SESSION["error"] = "All fields are required";
        header("Location:edit_student.php?id=" . $_SESSION["edit_student_id"] . "");
    }else{
        /*

        $query = "UPDATE students SET fullname = :fullname, gender = :gender, email = :email, class = :class";
        if(!empty($_FILES["picture"]["name"])){
            $query .= ", picture = :picture";
        }
        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":gender", $gender);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":class", $class);
        //$stmt->bindParam(":scholarship_status", $scholarship_status);
        if(!empty($_FILES["picture"]["name"])){
            $target_dir = "assets/images/";
            $target_file = $target_dir . basename( $_FILES["picture"]["name"] );
            $fullPath = $target_dir . $_FILES["picture"]["name"];
            move_uploaded_file( $_FILES["picture"]["tmp_name"], $target_file);
            $stmt->bindParam(":picture",$fullPath);
        }
        $stmt->bindParam(":id", $_SESSION["edit_student_id"]);
        $stmt->execute();*/
        
        $_SESSION["success"] = "Student details updated successfully";
        header("Location:edit_student.php?id=" . $_SESSION["edit_student_id"] . "");
    }
} else {
?>
    <title>Error - Quest Schools Admin</title>
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/quest.jpg" type="image/x-icon">
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
    </style>
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
        <h2><i class="bi bi-shield-fill-exclamation"></i></h2>
        <p>Sorry, the method used to request this page is not allowed.</p>
        <a href="./">Go to Homepage</a>
    </div>
<?php
}
