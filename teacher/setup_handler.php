<?php

if (isset($_POST["submit"])) {

    session_start();
    include "teacher_includes/autoloader.inc.php";
    include "teacher_includes/db.inc.php";
    include "teacher_includes/teacher.inc.php";

    $portal_code = $_POST["portal_code"];
    $email = $_POST["email"];
    $picture = $_FILES["picture"];
    $password = $_POST["pwd"];

    $data = [
        "portal_code" => $portal_code,
        "email" => $email,
        "picture" => $picture,
        "password" => $password,
        "hashedPassword"=>password_hash($password,PASSWORD_BCRYPT,["cost"=>10]),
        "picture_path"=>"assets/images/" . $picture["name"]
    ];

    define("TEACHER_SETUP", new TeacherSetup($pdo, $data));

    if (!TEACHER_SETUP->run()) {
        $_SESSION["error"] = "Error occured while setting up. Try again later!";
        header("Location: setup.php?portal_code=" . $data["portal_code"]);
    } else {
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES["picture"]["name"]);
        move_uploaded_file($_FILES["picture"]["tmp_name"],$target_file);
        $_SESSION["success"] = "Account setup completed";
        header("Location: login.php");
    }
}
/*



    

    $fullData = [
        "portal_code" => $portal_code,
        "email" => $email,
        "picture" => $picture,
        "password" => $password,
        "id" => $id,
        "picture_path" => "assets/images/" . $picture["name"],
        "hashedPassword" => password_hash($password, PASSWORD_BCRYPT, ["cost" => 10])
    ];

    $setup = new TeacherSetup($pdo, $fullData);
    if (!$setup->setup()) {
        $_SESSION["error"] = "Error occured. Check your inputs ";
        header("Location: setup.php?portal_code=" . $fullData["portal_code"] . "&id=" . $fullData["id"]);
    } else {
        unset($_SESSION["error"]);
        $_SESSION["success"] = "Account setup completed, login now.";
        header("Location: login.php");
    }
} else {
?>
Page blocked because it is accessed illegally
<?php
}
*/