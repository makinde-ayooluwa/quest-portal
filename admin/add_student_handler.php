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
        // header("Location: add_student.php");
        // exit();
    } 

    // 1. FIRST: Check if an email has already been sent to this admission number
    $alreadySent = $admin->checkIfEmailExists($pdo, $studentData['admission_number']);

    if ($alreadySent) {
        unset($_SESSION["success"]);
        $_SESSION['error'] = "An account setup email has already been sent to " . htmlspecialchars($studentData['fullname']);
        //header("Location: add_student.php");
        //exit();
    }

    // 2. SECOND: Attempt to add the student record to the database
    if (!$admin->addStudent($pdo, $studentData)) {
        unset($_SESSION["success"]);
        $_SESSION["error"] = "Error occurred while adding student. Student may exist already.";
        //header("Location: add_student.php");
        //exit();
    } 
    
    // 3. THIRD: If student is added and no email was sent before, fire the email utility
    unset($_SESSION["error"]);
    
    // NOTE: Verify your 'EmailUtils' constructor arguments. 
    // If it requires your PDO database reference, use $pdo instead of $host.
    $emailUtils = new EmailUtils($host); 
    
    $emailSent = $emailUtils->sendStudentSetupEmail(
        $studentData['email'],
        $studentData['fullname'],
        $studentData['admission_number']
    );

    if ($emailSent) {
        // 4. Log the email transaction to prevent future duplication
        $admin->logSentEmail($pdo, $studentData['admission_number']);

        $_SESSION["success"] = htmlspecialchars($studentData['fullname']) . " added and setup email sent successfully!";
        //header("Location: add_student.php");
        //exit();
    } else {
        // Handle network/SMTP connection failure visibility
        $_SESSION["error"] = "Student record created, but the Email delivery failed. Check your SMTP configurations.";
        //header("Location: add_student.php");
        //exit();
    }
}