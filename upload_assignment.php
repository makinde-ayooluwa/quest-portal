<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmentId = $_POST['assignment_id'] ?? null;
    $comments = $_POST['comments'] ?? '';

    if (!$assignmentId) {
        $_SESSION['error'] = 'Assignment ID is required.';
        header('Location: assignments.php');
        exit();
    }

    // Check if assignment exists and belongs to student's class
    $checkQuery = "SELECT id, title FROM assignments WHERE id = :id AND class_name = :class_name";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':id', $assignmentId);
    $checkStmt->bindParam(':class_name', $studentData['class']);
    $checkStmt->execute();

    if (!$checkStmt->fetch()) {
        $_SESSION['error'] = 'Assignment not found or not accessible.';
        header('Location: assignments.php');
        exit();
    }

    // Check if student already submitted
    $existingQuery = "SELECT id FROM assignment_submissions WHERE assignment_id = :assignment_id AND student_id = :student_id";
    $existingStmt = $pdo->prepare($existingQuery);
    $existingStmt->bindParam(':assignment_id', $assignmentId);
    $existingStmt->bindParam(':student_id', $studentData['id']);
    $existingStmt->execute();

    if ($existingStmt->fetch()) {
        $_SESSION['error'] = 'You have already submitted this assignment.';
        header('Location: assignments.php');
        exit();
    }

    // Handle file upload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'File upload failed.';
        header('Location: assignments.php');
        exit();
    }

    $file = $_FILES['file'];
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['error'] = 'Invalid file type. Only PDF, DOC, DOCX, JPG, PNG allowed.';
        header('Location: assignments.php');
        exit();
    }

    if ($file['size'] > $maxSize) {
        $_SESSION['error'] = 'File size too large. Maximum 10MB allowed.';
        header('Location: assignments.php');
        exit();
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = 'teacher/uploads/assignments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'assignment_' . $assignmentId . '_student_' . $studentData['id'] . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Insert submission record
        $insertQuery = "INSERT INTO assignment_submissions (assignment_id, student_id, submission_file, comments, submitted_at, status)
                       VALUES (:assignment_id, :student_id, :submission_file, :comments, NOW(), 'submitted')";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindParam(':assignment_id', $assignmentId);
        $insertStmt->bindParam(':student_id', $studentData['id']);
        $insertStmt->bindParam(':submission_file', $fileName);
        $insertStmt->bindParam(':comments', $comments);

        if ($insertStmt->execute()) {
            $_SESSION['success'] = 'Assignment submitted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to save submission record.';
            unlink($filePath); // Delete uploaded file if DB insert fails
        }
    } else {
        $_SESSION['error'] = 'Failed to upload file.';
    }

    header('Location: assignments.php');
    exit();
}

// If not POST request, redirect to assignments page
header('Location: assignments.php');
exit();
?>
