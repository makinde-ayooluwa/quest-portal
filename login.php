<?php

session_start();

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

        .bg-yellow {
            background: var(--quest-yellow);
        }

        .bg-green {
            background: var(--quest-green);
        }

        .form-container {
            background: #ffffffbe;
            width: 410px;
            border-radius: 15px;
            margin-top: 50px;
        }

        * {
            font-family: Montserrat;
        }

        label {
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section {
            background: rgba(255,255,255,0.8);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.1);
        }

        .section-title {
            color: var(--quest-green);
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--quest-green);
            box-shadow: 0 0 0 0.2rem rgba(90, 172, 123, 0.25);
        }

        .submit-btn {
            text-transform: uppercase;
            text-align: center;
            text-size-adjust: auto;
            font-display: block;
            color: #fff;
            font-weight: bolder;
            font-size: 15px;
            padding: 12px 30px;
            background: linear-gradient(45deg, var(--quest-green), var(--quest-yellow));
            border: none;
            outline: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            background: linear-gradient(45deg, var(--quest-yellow), var(--quest-green));
        }

        .mb-3 {
            margin-bottom: 1.5rem !important;
        }

        .password-toggle {
            border-left: none;
        }

        .password-toggle:focus {
            box-shadow: none;
        }
    </style>
</head>

<body class="bg-green">
    <!--<nav class="px-3 py-0 navbar navbar-expand border-bottom">
        <a href="./" class="navbar-brand">
            <img width="50" src="assets/images/quest.jpg" alt="">
        </a>
    </nav>-->
    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    } else if (isset($_SESSION["success"])) {
    ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
        </script>
    <?php
        unset($_SESSION["success"]);
    } else {
        echo "";
    }
    ?>
    <!--<div class="container shadow-sm form-container card">
        <div class="d-flex justify-content-center">
            <img width="50" src="assets/images/quest.jpg" alt="">
        </div>
        <div class="py-2">
            <h3 class="fs-3 fw-bolder text-center">Quest Portal</h3>
            <p class="fs-6 text-center fw-bold text-success">Access your portal securely</p>
            <div class="py-3">
                <form action="./login_handler.inc.php" method="post">
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
                            <a href="login.php" class="text-decoration-none text-green">Forgot password?</a>
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
                    <button type="submit" onclick="load(this)" class="submit-btn form-control">Login</button>
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
    </div>-->
    <style>
        .logo-header {
            display: grid;
            justify-content: center;
        }

        .logo-header .header {
            text-align: center;
            color: var(--quest-green);
            font-size: 40px;
        }

        .logo-header .header-text {
            color: var(--quest-yellow);
            font-size: 20px;
        }


        form {
            padding: 10px;
        }

        .real-header {
            margin-top: 50px;
            display: flex;
            justify-content: center;
        }

        .real-header img {
            width: 50px;
        }
    </style>
    <div class="real-header">
        <img src="assets/images/Quest logo.jpg" alt="Quest Portal Logo" style="width: 80px; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
    </div>
    <div class="container form-container card rounded-3">
        <div class="my-2">
            <div class="logo-header">
                <span class="header">
                    <i class="bi bi-door-open-fill"></i>
                </span>
                <p class="header-text">Login to access your portal</p>
            </div>
        </div>
        <div class="form p-4">
            <form action="login_handler.inc.php" id="loginForm" method="post" enctype="multipart/form-data">
                <!-- Login Credentials Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-sign-in-alt"></i>
                        Login Credentials
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" class="form-control" placeholder="Enter your email address" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Enter your password" id="password" name="password">
                            <button class="btn btn-outline-secondary password-toggle" type="button" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-danger">
                        <a href="forgot_password.php" class="text-decoration-none">
                            <i class="fas fa-key"></i>
                            Forgot password?
                        </a>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Password toggle functionality
        const password = document.querySelector("#password");
        const passwordToggle = document.querySelector("#passwordToggle");
        passwordToggle.addEventListener("click", function() {
            if (password.type == "password") {
                password.type = "text";
                passwordToggle.innerHTML = "<i class='fas fa-eye-slash'></i>";
            } else {
                password.type = "password";
                passwordToggle.innerHTML = "<i class='fas fa-eye'></i>";
            }
        });

        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
