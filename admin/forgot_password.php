<?php
session_start();

if(isset($_SESSION["admin"])){
    header("Location: ./");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password - Admin</title>
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
            <p class="fs-6 text-center fw-bold text-success">Reset your password</p>
            <div class="py-3">
                <form action="forgot_password_handler.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="mb-2 form-label">Email</label>
                        <input type="email" placeholder="Email address" id="email" name="email" class="form-control form-control-lg" required>
                    </div>
                    <input type="hidden" name="user_type" value="admin">
                    <div class="d-flex justify-content-between">
                        <a href="login.php" class="text-decoration-none text-green">Back to Login</a>
                        <button type="submit" class="btn btn-success">Send Reset Link</button>
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
