<?php
session_start();
set_time_limit(300); // Gives your script up to 5 minutes to finish sending all emails

include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentsFromSheet = json_decode(file_get_contents("php://input"), true);

    if (!is_array($studentsFromSheet)) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Invalid data received"]);
        exit;
    }

    // 1️⃣ Fetch current admission_numbers from DB
    $currentStudents = $pdo->query("SELECT admission_number FROM students")->fetchAll(PDO::FETCH_COLUMN);

    // Normalize current students (trim spaces and strip any escaped slashes)
    $currentStudents = array_map(function($val) {
        return trim(stripslashes($val));
    }, $currentStudents);

    // Normalize new admissions from sheet cleanly
    $newAdmissionNumbers = array_map(function ($s) {
        $adm = $s['admission_number'] ?? '';
        return trim(stripslashes($adm));
    }, $studentsFromSheet);

    // Compute actual deletions safely
    $toDelete = array_diff($currentStudents, $newAdmissionNumbers);

    // 3️⃣ Delete students removed from spreadsheet
    if (!empty($toDelete)) {
        $placeholders = rtrim(str_repeat('?,', count($toDelete)), ',');
        $stmt = $pdo->prepare("DELETE FROM students WHERE admission_number IN ($placeholders)");
        $stmt->execute(array_values($toDelete)); 
        
        // Refresh our memory list since records were removed from the database disk
        $currentStudents = array_diff($currentStudents, $toDelete);
    }

    // 4️⃣ Add new students and send emails
    $results = [];
    $emailUtils = new EmailUtils($host); 

    foreach ($studentsFromSheet as $data) {

        // Normalize data key maps using stripslashes to handle the "/" safely
        $admission     = trim(stripslashes($data['admission_number'] ?? ''));
        $fullNameField = trim($data["fullname"] ?? '');
        $emailField    = trim($data["email"] ?? '');
        $classField    = trim($data["class"] ?? '');

        if (empty($admission)) {
            continue; // Skip blank spacer lines safely
        }

        // Skip if already exists in our active database tracking array
        if (in_array($admission, $currentStudents)) {
            $results[] = [
                "email" => $emailField,
                "status" => "skipped",
                "message" => "Student already exists in database."
            ];
            continue;
        }

        $result = [
            "email" => $emailField,
            "status" => "pending",
            "message" => ""
        ];

        // Write student record
        if ($admin->addStudent($pdo, [
            "fullname" => $fullNameField,
            "email" => $emailField,
            "admission_number" => $admission,
            "class" => $classField
        ])) {
            try {
                // Execute mailing transaction
                $send = $emailUtils->sendStudentSetupEmail($emailField, $fullNameField, $admission);
                
                if ($send === false) {
                     throw new Exception("Local mail delivery handler rejected distribution request.");
                }

                $result['status'] = "success";
                $result['message'] = "Email sent successfully";

            } catch (\Throwable $t) {
                $result['status'] = "error";
                $result['message'] = "Student added, but email failed: " . $t->getMessage();
            }
        } else {
            $result['status'] = "error";
            $result['message'] = "Failed to add student record to database.";
        }

        $results[] = $result;
        
        // Minor pacing delay to protect connection limits on free hosting platforms
        usleep(200000); 
    }

    // Clear output payload buffers and output clean validation logs
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode($results);
    exit();

} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit();
}