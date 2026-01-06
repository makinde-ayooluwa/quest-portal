<?php
session_start();
require_once 'teacher_includes/autoloader.inc.php';
require_once 'teacher_includes/db.inc.php';
require_once 'teacher_includes/teacher.inc.php';

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academic_term = trim($_POST['academic_term']);
    $student_admission_number = trim($_POST['student_admission_number']);

    // Validate inputs
    if (empty($academic_term) || empty($student_admission_number)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: manage_results.php");
        exit();
    }

    // Check if file is uploaded
    if (!isset($_FILES['result_file']) || $_FILES['result_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Please select a valid file to upload.';
        header("Location: manage_results.php");
        exit();
    }

    $file = $_FILES['result_file'];

    // Validate file type (allow PDF, DOC, DOCX, etc.)
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/docx'];
    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['error'] = 'Invalid file type. Only PDF, Excel and Word documents are allowed.';
        header("Location: manage_results.php");
        exit();
    }

    // Validate file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        $_SESSION['error'] = 'File size too large. Maximum allowed size is 10MB.';
        header("Location: manage_results.php");
        exit();
    }

    // Check if student exists
    $studentQuery = "SELECT id FROM students WHERE admission_number = :admission_number";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->bindParam(':admission_number', $student_admission_number);
    $studentStmt->execute();
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['error'] = 'Student with the given admission number does not exist.';
        header("Location: manage_results.php");
        exit();
    }

    // Check if result already exists for this term and student
    $checkQuery = "SELECT id FROM results WHERE academic_term = :academic_term AND student_admission_number = :admission_number";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':academic_term', $academic_term);
    $checkStmt->bindParam(':admission_number', $student_admission_number);
    $checkStmt->execute();

    if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['error'] = 'Result for this academic term and student already exists.';
        header("Location: manage_results.php");
        exit();
    }

    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueFilename = uniqid('result_', true) . '.' . $fileExtension;
    $uploadDir = '../assets/uploads/results/';
    $uploadPath = $uploadDir . $uniqueFilename;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Insert into database
        $data = [
            'academic_term' => $academic_term,
            'student_admission_number' => $student_admission_number,
            'result_file' => $uniqueFilename
        ];

        if ($teacher->postResult($pdo, $data)) {
            $_SESSION['success'] = 'Result uploaded successfully.';
        } else {
            $_SESSION['error'] = 'Failed to save result to database.';
            // Delete uploaded file if database insert fails
            unlink($uploadPath);
        }
    } else {
        $_SESSION['error'] = 'Failed to upload file.';
    }

    header("Location: manage_results.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
