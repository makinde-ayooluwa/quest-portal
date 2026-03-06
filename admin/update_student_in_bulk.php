<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

$requestMethod = $_SERVER["REQUEST_METHOD"];
switch ($requestMethod) {
    case "POST":

        $studentsFromSheet = json_decode(file_get_contents("php://input"), true);

        if (!is_array($studentsFromSheet)) {
            echo json_encode(["status" => "error", "message" => "Invalid data"]);
            exit;
        }

        $success = [];
        $failed  = [];

        $emailUtils = new EmailUtils($host); // create once

        foreach ($studentsFromSheet as $student) {

            // 🔒 Validate required fields
            if (
                empty($student['email']) ||
                empty($student['fullname']) ||
                empty($student['class']) ||
                empty($student['admission_number'])
            ) {
                $failed[] = $student;
                continue;
            }

            // 🔍 Check if student exists by admission number (primary identifier)
            $checkStmt = $pdo->prepare(
                "SELECT id, email FROM students WHERE admission_number = :admission"
            );
            $checkStmt->execute([':admission' => $student['admission_number']]);
            $existingStudent = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingStudent) {
                // ✅ UPDATE by admission_number
                $updateStmt = $pdo->prepare(
                    "UPDATE students 
                     SET fullname = :fullname, class = :class, email = :email 
                     WHERE admission_number = :admission"
                );

                $updated = $updateStmt->execute([
                    ':fullname' => $student['fullname'],
                    ':class' => $student['class'],
                    ':email' => $student['email'],
                    ':admission' => $student['admission_number']
                ]);

                if (!$updated || $updateStmt->rowCount() === 0) {
                    $failed[] = $student; // update did nothing
                    continue;
                }
            } else {
                // ✅ INSERT new student
                if (!$admin->addStudent($pdo, $student)) {
                    $failed[] = $student;
                    continue;
                }
            }

            // 📧 Send email (optional)
            try {
                $emailUtils->sendStudentSetupEmail(
                    $student['email'],
                    $student['fullname'],
                    $student['admission_number']
                );
            } catch (Exception $e) {
                // ignore email errors
            }


            $success[] = $student['admission_number'];
        }

        echo json_encode([
            'status' => empty($failed) ? 'success' : 'partial',
            'updated_or_added' => count($success),
            'failed' => count($failed)
        ]);

        break;
}
