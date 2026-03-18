<?php
session_start();

if (isset($_SESSION['student'])) {
    header('Location: ./');
    exit();
}

$token = $_GET['token'] ?? '';
$user_type = $_GET['type'] ?? '';

if (empty($token) || empty($user_type)) {
    $_SESSION['error'] = "Invalid reset link.";
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Reset Password - Quest Portal</title>
    <style>
        .form-container {
            max-width: 442px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            margin-top: 100px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container form input {
            transition: box-shadow 0.3s ease-in-out;
        }

        .input-error {
            border: none;
            outline: 0;
            box-shadow: 0 0 0 0.1rem rgba(255, 0, 0, 0.56);
        }

        .input-error:focus {
            box-shadow: 0 0 0 0.2rem rgba(255, 0, 0, 0.76);
        }
    </style>
</head>

<body class="">
    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <script>
            toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
    ?>
        <script>
            toastr.success("<?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>", 'Success!');
        </script>
    <?php
        unset($_SESSION['success']);
    }
    ?>
    <div class="justify-content-center align-items-center">
        <div class="container form-container shadow-lg">
            <div class="d-flex justify-content-center">
                <img src="assets/images/quest.jpg" width="50" alt="">
            </div>
            <div class="my-3">
                <h2 class="text-center fw-bold">Quest Portal</h2>
                <p class="fs-6 text-center fw-bold text-success">Enter New Password</p>
            </div>
            <div class="my-1">
                <form action="reset_password_handler.php" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="login.php" class="text-decoration-none">Back to Login</a>
                            </div>
                            <div>
                                <button class="btn btn-sm px-4 py-2 btn-success fw-bolder" type="submit">RESET PASSWORD</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
