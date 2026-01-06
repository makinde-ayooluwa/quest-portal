<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_GET["portal_code"])/* || !isset($_GET["id"])*/) {
?>
Page blocked
<?php
}elseif($_GET["portal_code"] == ""){
    ?>
    Page blocked
    <?php
}

elseif (/*$admin->getSpecificAdmin($pdo, $_GET["id"]) && */$admin->getSpecificAdmin($pdo, $_GET["portal_code"])["portal_code"] != $_GET["portal_code"]) {
?>
    Page blocked
<?php
} elseif ($admin->getSpecificAdmin($pdo, $_GET["portal_code"])["staff_role"] !== "Admin") {
?>
    Page blocked
<?php
} elseif ($admin->getSpecificAdmin($pdo, $_GET["portal_code"])["account_verification"] == "Verified") {
?>
    Admin is verified
<?php
} else {
    $data = $admin->getSpecificAdmin($pdo, $_GET["portal_code"]);
    $_SESSION["current_id"] = $data["id"];
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php include "head.php"; ?>
        <title>Account Setup - Quest Admin</title>
        <style>
            .form-container {
                border-radius: 12px;
                width: 442px;
                margin-top: 50px;
            }
        </style>
    </head>

    <body class="bg-green">
        <?php
        if (isset($_SESSION["error"])) {
        ?>
            <script>
                toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
            </script>
        <?php
            unset($_SESSION["error"]);
        } elseif (isset($_SESSION["success"])) {
            header("Location: login.php");
            unset($_SESSION["success"]);
        }
        ?>
        <div class="container p-2 form-container card rounded-2">
            <div class="py-3 d-flex justify-content-center">
                <img src="assets/images/quest.jpg" alt="" width="15%">
            </div>
            <form action="setup_handler.php" method="post" enctype="multipart/form-data">
                <h1 class="fs-4 text-center">Setup your account</h1>
                <div class="my-2">
                    <div class="mb-2">
                        <label for="Pportal_code" class="form-label">Portal Code</label>
                        <input type="text" id="Pportal_code" name="portal_code" readonly value="<?php echo $_GET["portal_code"] ?>" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label for="Eemail" class="form-label">Email</label>
                        <input type="email" id="Eemail" name="email" readonly class="form-control" value="<?php echo $data["email"] ?>">
                    </div>
                    <div class="mb-2">
                        <label for="Ppicture" class="from-label">Profile picture</label>
                        <input type="file" name="picture" id="Ppicture" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" name="pwd">
                    </div>
                    <div class="mb-3" id="password_requirement">
                        <ul>
                            <li id="digit">Must have at least one digit</li>
                            <li id="upper">Must have at least one uppercase letter</li>
                            <li id="lower">Must have at least one lowercase letter</li>
                            <li id="length">Must be a minimum of 8 length</li>
                        </ul>
                    </div>
                    <script>
                        const password = document.querySelector("form input#password");
                        const requirements = document.querySelector("form #password_requirement ul");
                        password.addEventListener("input", function() {
                            if (!(/[0-9]/.test(password.value))) {
                                requirements.querySelector("li#digit").style.color = "red";
                                //password_error = "No digit";
                            } else {
                                requirements.querySelector("li#digit").style.color = "green";
                            }
                            if (!(/[A-Z]/.test(password.value))) {
                                requirements.querySelector("li#upper").style.color = "red";
                                //password_error = "No uppercase";
                            } else {
                                requirements.querySelector("li#upper").style.color = "green";
                            }
                            if (!(/[a-z]/.test(password.value))) {
                                requirements.querySelector("li#lower").style.color = "red";
                                //password_error = "No lowercase";
                            } else {
                                requirements.querySelector("li#lower").style.color = "green";
                            }
                            if (!(password.value.length >= 8)) {
                                requirements.querySelector("li#length").style.color = "red";
                                //password_error = "Not more than 8";
                            } else {
                                requirements.querySelector("li#length").style.color = "green";
                            }
                        });
                    </script>
                    <div class="mb-3 px-3">
                        <div class="d-flex justify-content-end">
                            <input type="submit" name="submit" class="btn btn-sm px-3 py-1 btn-success" value="Setup Account">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    </body>

    </html>
<?php
}
?>