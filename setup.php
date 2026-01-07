<?php
require_once 'student_includes/db.inc.php';
function getIfStudentIsReal($pdo, $admission_number)
{
    $query = "SELECT * FROM students WHERE admission_number = :admission_number;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":admission_number", $admission_number);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}

function getVerified($pdo, $admission_number)
{
    $query = "SELECT * FROM students WHERE admission_number = :admission_number;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":admission_number", $admission_number);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result["account_verification"] == "Verified") {
        return true;
    } else {
        return false;
    }
}
$block_reason = "";
if (!isset($_GET["admission_number"])) {
    $block_reason = "Invalid access. Please provide a valid admission number.";
} else if (!getIfStudentIsReal($pdo, $_GET["admission_number"])) {
    $block_reason = "Student not found. Please check your admission number.";
} else if (getVerified($pdo, $_GET["admission_number"])) {
    $block_reason = "Your account is already set up and verified.";
}

if ($block_reason !== "") {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Access Blocked</title>
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

            .blocked-container {
                background: #ffffffbe;
                width: 100%;
                max-width: 600px;
                border-radius: 15px;
                margin-top: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                text-align: center;
                padding: 40px 20px;
            }

            * {
                font-family: Montserrat;
            }

            .blocked-icon {
                font-size: 4rem;
                color: var(--quest-green);
                margin-bottom: 20px;
            }

            .blocked-title {
                color: var(--quest-green);
                font-weight: bold;
                font-size: 24px;
                margin-bottom: 15px;
            }

            .blocked-message {
                color: #666;
                font-size: 16px;
                margin-bottom: 30px;
            }

            .back-btn {
                text-transform: uppercase;
                text-align: center;
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
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                text-decoration: none;
                display: inline-block;
            }

            .back-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
                background: linear-gradient(45deg, var(--quest-yellow), var(--quest-green));
            }
        </style>
    </head>

    <body class="bg-green">
        <div class="real-header">
            <img src="assets/images/quest.jpg" alt="Quest Portal Logo" style="width: 80px; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        </div>
        <div class="container blocked-container card rounded-3">
            <div class="my-2">
                <div class="logo-header">
                    <span class="header">
                        <i class="bi bi-shield-lock-fill blocked-icon"></i>
                    </span>
                    <h2 class="blocked-title">Access Blocked</h2>
                    <p class="blocked-message"><?php echo $block_reason; ?></p>
                    <a href="login.php" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
        <script>
            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        </script>
    </body>

    </html>
<?php
} else {
?>
    <!-- Real Page
    ________
   /
    -->
    <?php

    session_start();

    require_once 'student_includes/autoloader.inc.php';
    require_once 'student_includes/db.inc.php';

    $query = "SELECT * FROM students WHERE admission_number = :admission_number;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":admission_number", $_GET["admission_number"]);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <title>SetUp Account</title>
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
                width: 100%;
                max-width: 600px;
                border-radius: 15px;
                margin-top: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
                background: rgba(255, 255, 255, 0.8);
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.1);
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

            .form-control,
            .form-select {
                border-radius: 8px;
                border: 2px solid #e9ecef;
                transition: all 0.3s ease;
            }

            .form-control:focus,
            .form-select:focus {
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
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }

            .submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
                background: linear-gradient(45deg, var(--quest-yellow), var(--quest-green));
            }

            .mb-3 {
                margin-bottom: 1.5rem !important;
            }

            .row.mb-3 .col-md-12 {
                margin-bottom: 1rem;
            }

            .row.mb-3 .col-md-12:last-child {
                margin-bottom: 0;
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
        if (isset($_SESSION["error"])) {
        ?>
            <script>
                toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
            </script>
        <?php
            unset($_SESSION["error"]);
        } elseif (isset($_SESSION["success"])) {
        ?>
            <script>
                toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
            </script>
        <?php
        }
        ?>

        <div class="real-header">
            <img src="assets/images/quest.jpg" alt="Quest Portal Logo" style="width: 80px; height: auto; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        </div>
        <div class="container form-container card rounded-3">
            <div class="my-2">
                <div class="logo-header">
                    <span class="header">
                        <i class="bi bi-door-open-fill"></i>
                    </span>
                    <p class="header-text">Setup your portal account</p>
                </div>
            </div>
            <div class="form p-4">
                <form action="portal_setup.php" id="loginForm" method="post" enctype="multipart/form-data">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </div>
                        <div class="mb-3">
                            <label for="admission_number" class="form-label">
                                <i class="fas fa-id-card"></i>
                                Admission number / ID
                            </label>
                            <input readonly class="form-control" placeholder="Admission number" value="<?php echo isset($_GET["admission_number"]) ? $_GET["admission_number"] : "" ?>" id="admission_number" name="admission_number">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input readonly class="form-control" placeholder="Email address" value="<?php echo $result["email"] ?>" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">
                                <i class="fas fa-user-tag"></i>
                                Fullname
                            </label>
                            <input type="text" class="form-control" id="fullname" readonly name="fullname" value="<?php echo $result["fullname"] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">
                                <i class="fas fa-birthday-cake"></i>
                                Date of Birth
                            </label>
                            <input type="date" class="form-control" name="dob">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars"></i>
                                Gender
                            </label>
                            <select name="gender" id="" class="form-select">
                                <option value=""> ---- Select Gender ---- </option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="picture" class="form-label">
                                <i class="fas fa-camera"></i>
                                Profile Picture
                            </label>
                            <input type="file" name="picture" accept=".jpg,.jpeg" class="form-control">
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-phone"></i>
                            Contact Information
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-mobile-alt"></i>
                                Phone
                            </label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Address
                            </label>
                            <input type="text" name="home_address" class="form-control">
                        </div>
                    </div>

                    <!-- Parents' Information Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-users"></i>
                            Parents' Information
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-friends"></i>
                                    Father's Name
                                </label>
                                <input type="text" class="form-control" name="father_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Father's Email
                                </label>
                                <input type="email" class="form-control" name="father_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Father's Phone
                                </label>
                                <input type="tel" class="form-control" name="father_phone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-friends"></i>
                                    Mother's Name
                                </label>
                                <input type="text" class="form-control" name="mother_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Mother's Email
                                </label>
                                <input type="email" class="form-control" name="mother_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Mother's Phone
                                </label>
                                <input type="tel" class="form-control" name="mother_phone" required>
                            </div>
                        </div>
                    </div>

                    <!-- Account Setup Section -->
                    <div class="form-section">
                        <div class="section-title">
                            <i class="fas fa-lock"></i>
                            Account Setup
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-key"></i>
                                Password
                            </label>
                            <input type="password" class="form-control" placeholder="Password" id="password" name="password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-user-plus me-2"></i>
                            Setup Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        </script>
    </body>

    </html>
<?php
}
?>
