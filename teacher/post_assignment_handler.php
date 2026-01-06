<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION["teacher"];
    $teacher = new Teacher($email);
    $teacherData = $teacher->getTeacherData($pdo, $email);

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = trim($_POST['subject']);
    $class_name = trim($_POST['class_name']);
    $due_date = trim($_POST['due_date']);

    // Validate required fields
    if (empty($title) || empty($subject) || empty($class_name) || empty($due_date)) {
        $_SESSION['error'] = "All required fields must be filled.";
        header("Location: post_assignment.php");
        exit();
    }

    // Validate due date is not in the past
    if (strtotime($due_date) < strtotime(date('Y-m-d'))) {
        $_SESSION['error'] = "Due date cannot be in the past.";
        header("Location: post_assignment.php");
        exit();
    }

    // Handle file upload
    $file_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/assignments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['file']['name']);
        $file_path = $upload_dir . time() . '_' . $file_name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $_SESSION['error'] = "Failed to upload file.";
            header("Location: post_assignment.php");
            exit();
        }
    }

    // Prepare assignment data
    $assignmentData = [
        'title' => $title,
        'description' => $description,
        'subject' => $subject,
        'class_name' => $class_name,
        'due_date' => $due_date,
        'file_path' => $file_path,
        'created_by' => $teacherData['id']
    ];

    // Post assignment
    if ($teacher->postAssignment($pdo, $assignmentData)) {
        $_SESSION['success'] = "Assignment posted successfully.";
        header("Location: post_assignment.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to post assignment. Please try again.";
        header("Location: post_assignment.php");
        exit();
    }
} else {
    header("Location: post_assignment.php");
    exit();
}
?>
