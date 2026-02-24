<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";
$id = $_GET["id"];

$dbData = $admin->getSpecificStudent($pdo, $id);

$emailUtils = new EmailUtils();
$emailSent = $emailUtils->sendStudentSetupEmail(
    $dbData['email'],
    $dbData['fullname'],
    $dbData['admission_number']
);
$query = "SELECT * FROM sent_emails WHERE admission_number = :admission_number";
$stmt = $pdo->prepare($query);
$stmt->execute([$dbData["admission_number"]]);
if (!$stmt->fetchAll(PDO::FETCH_ASSOC) > 0) {
    if ($emailSent) {
        $_SESSION["success"] = "Setup email sent successfully";
        $stmt = $pdo->prepare("INSERT INTO sent_emails (admission_number) VALUES (:admission_number)");
        $stmt->bindParam(":admission_number", $dbData["admission_number"]);
        $stmt->execute();
    } else {
        $_SESSION["success"] = "Setup email could not be sent";
    }
}
header("Location: students.php");
