<?php

session_start();

if(isset($_SESSION["admin"])){
    header("Location: ./");
}

/*require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
$mail = new PHPMailer(true); // true enables exceptions for error handling
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com'; // Or your SMTP server host
$mail->SMTPAuth   = true;
$mail->Username   = 'makindeayooluwa604@gmail.com';
$mail->Password   = 'lirw zgkb kegs xyat'; // Use an app password for Gmail
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS
$mail->Port       = 587; // or 465 for SMTPS
$mail->setFrom('makindeayooluwa604@gmail.com', 'Makinde Ayooluwa');
$mail->addAddress('makindeayooluwa42@gmail.com', 'Makinde Ayooluwa');
// Optional: addReplyTo, addCC, addBCC
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = 'Subject of your email';
$mail->Body    = 'This is the <b>HTML body</b> of the email.';
$mail->AltBody = 'This is the plain text body for non-HTML mail clients.';
//$mail->addAttachment('/vendor/phpmailer/docs/README.md', 'document.pdf');
try {
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>LOGIN</title>
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

        .error-loader {
            position: fixed;
            left: 100%;
            transition-property: left;
            transition-duration: 0.75s;
            opacity: 1;
            padding: 0px;
            top: 2.5%;
            width: 25%;
            border-radius: 5px;
            background: #ff5353d0;
            z-index: 20;
        }

        .error-loader .loader {
            animation: load 4s linear;
            border-bottom-left-radius: inherit;
            border-bottom-right-radius: inherit;
            width: 100%;
        }

        @keyframes load {
            to {
                width: 0%;
            }
        }

        .error-loader .loader .loader-bar {
            border-bottom-left-radius: inherit;
            border-bottom-right-radius: inherit;
            width: inherit;
            height: 6px;
            background-color: #6f0000ff;
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
    <!--<div class="error-loader">
        <div class="d-flex justify-content-start p-2">
            <div class="text-white p-1">
                <h1 class="display-4">404</h1>
            </div>
            <div class="d-grid">
                <span class="text-start fw-bold fs-4 text-white">Error</span>
                <span class="text-white fs-6">An error occured while subscribing</span>
            </div>
        </div>
        <div class="loader">
            <div class="loader-bar"></div>
        </div>
        <script>
            const loader = document.querySelector(".error-loader");
            setTimeout(() => {
                $(".error-loader").css("opacity","0");
            }, 2500);
            setTimeout(() => {
                document.body.removeChild(loader);
            }, 3900);
        </script>
    </div>-->
    <!--<nav class="px-3 py-0 navbar navbar-expand border-bottom">
        <a href="./" class="navbar-brand">
            <img width="50" src="assets/images/quest.jpg" alt="">
        </a>
    </nav>-->
    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <!--<div class="error-loader error">
            <div class="d-flex justify-content-start p-2">
                <div class="text-white p-1">
                    <i class="bi bi-shield-fill-exclamation display-4"></i>
                </div>
                <div class="d-grid">
                    <span class="text-start fw-bold fs-4 text-white">Error</span>
                    <span class="text-white fs-6"><?php //echo $_SESSION["error"] 
                                                    ?></span>
                </div>
            </div>
            <div class="loader">
                <div class="loader-bar"></div>
            </div>
            <script>
                const loader = document.querySelector(".error-loader");
                setTimeout(() => {
                    $(".error-loader").css("left", "74%");
                }, 10);
                setTimeout(() => {
                    $(".error-loader").css("left", "100%");
                }, 2500);
                setTimeout(() => {
                    document.body.removeChild(loader);
                }, 3900);
            </script>
        </div>-->
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    } elseif (isset($_SESSION["success"])) {
    ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>","Success!");
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
            <p class="fs-6 text-center fw-bold text-success">Access your portal securely</p>
            <div class="py-3">
                <form action="./login_handler.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="mb-2 form-label">Email</label>
                        <input type="email" placeholder="Email address" id="email" name="email" class="form-control form-control-lg">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="mb-2 form-label">Password</label>
                        <div class="input-group">
                            <input type="password" placeholder="Password" id="password" name="password" class="form-control form-control-lg">
                            <button class="text-success fs-5 password-toggle input-group-text" type="button" id="passwordToggle"><i class="bi bi-eye"></i></button>
                        </div>
                        <script>
                            const password = document.querySelector("#password");
                            const passwordToggle = document.querySelector("#passwordToggle");
                            passwordToggle.addEventListener("click", function() {
                                if (password.type == "password") {
                                    password.type = "text";
                                    passwordToggle.innerHTML = "<i class='bi bi-eye-slash'></i>";
                                } else {
                                    password.type = "password";
                                    passwordToggle.innerHTML = "<i class='bi bi-eye'></i>";
                                }
                            })
                        </script>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="pb-3">
                            <a href="forgot_password.php" class="text-decoration-none text-green">Forgot password?</a>
                        </div>
                    </div>
                    <style>
                        .submit-btn {
                            position: relative;
                            background: var(--quest-green);
                            color: white;
                            font-weight: bold;
                            bottom: 0px;
                            border: none;
                            padding: 10px;
                            font-size: 1.1rem;
                            border-radius: 5px;
                            transition: all 0.4s ease;
                            display: flex;
                            justify-content: center;
                        }

                        .submit-btn:disabled {
                            background: #525151ff;
                        }

                        .submit-btn:disabled:hover {
                            bottom: 0px;
                            box-shadow: none;
                            background: #525151ff;
                            color: white;
                        }

                        .submit-btn:hover {
                            bottom: 3px;
                            box-shadow: 0px 5px 5px 0px #5aac7b96;
                            background: var(--quest-yellow);
                            color: white;
                        }

                        .spinner {
                            margin-right: 10px;
                            width: 30px;
                            height: 30px;
                            border-radius: 50%;
                            border: 5px solid transparent;
                            border-top-color: #0c00adff;
                            background: transparent;
                            animation: spin 0.75s linear infinite;
                        }

                        @keyframes spin {
                            to {
                                transform: rotate(360deg)
                            }
                        }
                    </style>
                    <button type="submit" class="submit-btn form-control">Login</button>
                    <script>
                        const form = document.querySelector('form');
                        const emailInput = form.querySelector("#email");
                        const pwdInput = form.querySelector("#password");
                        const btn = form.querySelector(".submit-btn");
                        form.addEventListener("submit", function(e) {
                            if (emailInput.value == "" || pwdInput.value == "") {} else {
                                e.preventDefault();
                                btn.innerHTML = "<div class='spinner'></div> Logging in...";
                                btn.setAttribute("disabled", true);
                                setTimeout(() => {
                                    form.submit();
                                }, 2000);
                            }
                        })
                    </script>
                </form>
            </div>
        </div>
    </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
