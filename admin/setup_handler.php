<?php

if (isset($_POST["submit"])) {

    session_start();
    include "admin_includes/autoloader.inc.php";
    include "admin_includes/db.inc.php";
    include "admin_includes/admin.inc.php";

    $portal_code = $_POST["portal_code"];
    $email = $_POST["email"];
    $picture = $_FILES["picture"];
    $password = $_POST["pwd"];
    $id = $_SESSION["current_id"];

    $data = [
        "portal_code" => $portal_code,
        "email" => $email,
        "picture" => $picture,
        "password" => $password,
        "hashedPassword"=>password_hash($password,PASSWORD_BCRYPT,["cost"=>10]),
        "picture_path"=>"assets/images/" . $picture["name"]
    ];

    define("ADMIN_SETUP", new AdminSetup($pdo, $data));

    if (!ADMIN_SETUP->run()) {
        $_SESSION["error"] = "Error occured while setting up. Try again later!";
        header("Location: setup.php?portal_code=" . $data["portal_code"] . "&id=" . $id);
    } else {
        // Secure file upload: validate extension and size
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $fileExt = strtolower(pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExt)) {
            $_SESSION["error"] = "Invalid image type. Only JPG, JPEG, PNG, WEBP, GIF allowed.";
            header("Location: setup.php?portal_code=" . $data["portal_code"] . "&id=" . $id);
            exit();
        }
        if ($_FILES["picture"]["size"] > 3 * 1024 * 1024) {
            $_SESSION["error"] = "Image too large. Maximum 3MB allowed.";
            header("Location: setup.php?portal_code=" . $data["portal_code"] . "&id=" . $id);
            exit();
        }
        $target_dir = "assets/images/";
        $newFileName = 'admin_setup_' . time() . '_' . rand(1000, 9999) . '.' . $fileExt;
        $target_file = $target_dir . $newFileName;
        move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
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

    $setup = new AdminSetup($pdo, $fullData);
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