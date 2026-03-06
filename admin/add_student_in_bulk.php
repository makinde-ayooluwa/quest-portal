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

/* 
This page covers both the addition and deletion of students
*/
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentsFromSheet = json_decode(file_get_contents("php://input"), true);

    if (!is_array($studentsFromSheet)) {
        echo json_encode(["status" => "error", "message" => "Invalid data"]);
        exit;
    }

    // 1️⃣ Fetch current admission_numbers from DB
    $currentStudents = $pdo->query("SELECT admission_number FROM students")->fetchAll(PDO::FETCH_COLUMN);

    // Normalize current students as strings
    $currentStudents = array_map('trim', $currentStudents);

    // Normalize new admissions from sheet as strings
    $newAdmissionNumbers = array_map(function ($s) {
        return trim($s['admission_number']);
    }, $studentsFromSheet);

    // Now compute deletions
    $toDelete = array_diff($currentStudents, $newAdmissionNumbers);
    // 3️⃣ Delete students removed from spreadsheet

    if (!empty($toDelete)) {
        $placeholders = rtrim(str_repeat('?,', count($toDelete)), ',');
        $stmt = $pdo->prepare("DELETE FROM students WHERE admission_number IN ($placeholders)");
        $stmt->execute(array_values($toDelete)); // Use array_values to avoid issues with keys
    }


    // 4️⃣ Add new students and send emails
    $results = [];

    foreach ($studentsFromSheet as $data) {

        $admission = $data['admission_number'];

        // Skip if already exists
        if (in_array($admission, $currentStudents)) continue;

        $result = [
            "email" => $data['email'] ?? null,
            "status" => "pending",
            "message" => ""
        ];

        if ($admin->addStudent($pdo, [
            "fullname" => $data["fullname"],
            "email" => $data["email"],
            "admission_number" => $admission,
            "class" => $data["class"]
        ])) {
            try {
                $emailUtils = new EmailUtils($host);
                $emailUtils->sendStudentSetupEmail($data['email'], $data['fullname'], $admission);
                $result['status'] = "success";
                $result['message'] = "Email sent successfully";
            } catch (Exception $e) {
                $result['status'] = "error";
                $result['message'] = $e->getMessage();
            }
        } else {
            $result['status'] = "error";
            $result['message'] = "Failed to add student";
        }

        $results[] = $result;
    }

    // Return results and summary
    echo json_encode([
        "status" => "completed",
        "added" => count($results),
        "deleted" => count($toDelete),
        "results" => $results
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}
