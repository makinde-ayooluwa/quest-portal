<?php
session_start();
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

    if (empty($fullname) || empty($email) || empty($admission_number) || empty($class)) {
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
            $emailUtils = new EmailUtils($host);
            $emailSent = $emailUtils->sendStudentSetupEmail(
                $studentData['email'],
                $studentData['fullname'],
                $studentData['admission_number']
            );
            // 1. Check if an email has already been sent to this specific student
            // Assuming your method takes the student's email or ID as an argument
            $alreadySent = $admin->checkIfEmailExists($pdo, $studentData['email']);

            if ($alreadySent) {
                // Stop here and set a warning message
                $_SESSION['error'] = "An email has already been sent to " . $studentData['fullname'];
                header("Location: your_page.php");
                exit();
            } else {
                // 2. If NOT already sent, proceed to send the email
                // (Insert your PHPMailer or mail() logic here to set $emailSent)

                if ($emailSent) {
                    // 3. Log the email in your database so it can't be sent again next time
                    $admin->logSentEmail($pdo, $studentData['email']);

                    // 4. Set your success message
                    $_SESSION["success"] = $studentData['fullname'] . " added and email sent successfully";

                    /* CRITICAL FIX: 
          Do NOT run unset($_SESSION['success']) immediately here! 
          If you unset it now, it will be deleted before your next page (HTML) can display it. 
          Unset it at the very top of your HTML view page after rendering it.
        */

                    //header("Location: your_page.php");
                    //exit();
                }
            }
        }
    }
}
