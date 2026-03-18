<?php
session_start();

if (isset($_SESSION['admin'])) {
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
    <title>Reset Password - Admin</title>
    <?php include "head.php" ?>
    <style>
        :root {
            --quest-yellow: #fec511;
            --quest-green: #5aac7b;
        }

        .text-green {
            color: var(--quest-green);
        }

        .text-yellow {
            color: var(--quest-yellow);
        }

        .bg-grad {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .btn-grad {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
        }

        .form-container {
            background: #ffffffff;
            padding-top: 25px;
            padding-bottom: 25px;
            width: 410px;
            border-radius: 15px;
            margin-top: 70px;
        }

        * {
            font-family: Montserrat;
        }

        label {
            font-weight: 900;
        }

        @media(max-width:1020px) {
            .error-loader {
                left: 50%;
                width: 45%;
            }
        }
    </style>
</head>

<body class="bg-green">
    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <script>
            toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    } elseif (isset($_SESSION["success"])) {
    ?>
        <script>
            toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>","Success!");
        </script>
    <?php
    unset($_SESSION['success']);
    }
    ?>
    <div class="container shadow-sm form-container card">
        <div class="d-flex justify-content-center">
            <img width="50" src="assets/images/quest.jpg" alt="">
        </div>
        <div class="py-2">
            <h3 class="fs-3 fw-bolder text-center">Quest Portal - Admin</h3>
            <p class="fs-6 text-center fw-bold text-success">Enter New Password</p>
            <div class="py-3">
                <form action="reset_password_handler.php" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">
                    <div class="mb-3">
                        <label for="password" class="mb-2 form-label">New Password</label>
                        <input type="password" placeholder="New Password" id="password" name="password" class="form-control form-control-lg" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="mb-2 form-label">Confirm New Password</label>
                        <input type="password" placeholder="Confirm New Password" id="confirm_password" name="confirm_password" class="form-control form-control-lg" required minlength="6">
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="login.php" class="text-decoration-none text-green">Back to Login</a>
                        <button type="submit" class="btn btn-success">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>
</html>
