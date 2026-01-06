<?php
session_start();

if (isset($_SESSION['teacher'])) {
    header('Location: ./');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Login - Teacher || Quest Portal</title>
    <style>
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .preloader .loader {
            border: 10px solid #f3f3f3;
            border-top: 16px solid var(--quest-green);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 0.5s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

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

<body class="bg-green">
    <!--<div class="preloader">
        <div class="loader"></div>
    </div>
    <script>
        $(window).on('load', function() {
            setTimeout(() => {
                $('.preloader').fadeOut('slow');
            }, 3000);
            setTimeout(() => {
                $('.preloader').remove();
                $('#main-content').removeClass('d-none');
            }, 3700);
        });
    </script>-->
    <!-- Add a d-none class after editing -->
    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
    ?>
        <script>
            toastr.success("<?php echo $_SESSION['success'] ?>", 'Success!');
        </script>
    <?php
        unset($_SESSION['success']);
    }
    ?>
    <div id="main-content" class="justify-content-center align-items-center">
        <div class="container form-container">
            <div class="d-flex justify-content-center">
                <img src="assets/images/quest.jpg" width="50" alt="">
            </div>
            <div class="my-3">
                <h2 class="text-center fw-bold">Quest Portal - Teacher</h2>
                <p class="fs-6 text-center fw-bold text-success">Login to access your portal securly</p>
            </div>
            <div class="my-1">
                <form action="login_handler.php" id="login_form" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                            </div>
                            <div>
                                <input class="btn btn-sm px-4 py-2 btn-success fw-bolder" name="submit" type="submit" value="LOGIN">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>