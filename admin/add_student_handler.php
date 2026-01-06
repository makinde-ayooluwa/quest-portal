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
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $admission_number = $_POST['admission_number'];
    $class = $_POST['class'];

    $studentData = [
        'fullname' => $fullname,
        'email' => $email,
        'admission_number' => $admission_number,
        'class' => $class
    ];

    if (empty($fullname) ||empty($email) || empty($admission_number) || empty($class)) {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Fill out all required fields";
        header("Location: add_student.php");
    } else {
        if (!$admin->addStudent($pdo, $studentData)) {
            unset($_SESSION["success"]);
            $_SESSION["error"] = "Error occured while adding student. Student may exist before";
            header("Location: add_student.php");
        } else {
            unset($_SESSION["error"]);

            // Send setup email to student
            $emailUtils = new EmailUtils();
            $emailSent = $emailUtils->sendStudentSetupEmail(
                $studentData['email'],
                $studentData['fullname'],
                $studentData['admission_number']
            );

            if ($emailSent) {
                $_SESSION["success"] = "Student added successfully and setup email sent";
            } else {
                $_SESSION["success"] = "Student added successfully, but email could not be sent";
            }
            header("Location: add_student.php");
        }
    }
}
