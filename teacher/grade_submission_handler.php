<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_assignments.php");
    exit();
}

if (!isset($_POST['grade_submission']) || !isset($_POST['submission_id']) || !isset($_POST['grade']) || !isset($_POST['assignment_id'])) {
    $_SESSION['error'] = "Missing required parameters.";
    header("Location: view_assignments.php");
    exit();
}

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);

if (!$teacherData) {
    $_SESSION['error'] = "Teacher data not found.";
    header("Location: login.php");
    exit();
}

$submissionId = (int)$_POST['submission_id'];
$assignmentId = (int)$_POST['assignment_id'];
$grade = trim($_POST['grade']);
$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

// Validate grade
if (empty($grade)) {
    $_SESSION['error'] = "Grade is required.";
    header("Location: view_submissions.php?assignment_id=" . $assignmentId);
    exit();
}

// Verify the submission exists
$query = "SELECT * FROM assignment_submissions WHERE id = :submission_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':submission_id', $submissionId);
$stmt->execute();
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    $_SESSION['error'] = "Submission not found.";
    header("Location: view_assignments.php");
    exit();
}

// Verify the submission belongs to the assignment
if ($submission['assignment_id'] != $assignmentId) {
    $_SESSION['error'] = "Invalid assignment for this submission.";
    header("Location: view_submissions.php?assignment_id=" . $assignmentId);
    exit();
}

// Verify the assignment exists and belongs to the teacher
$query = "SELECT created_by FROM assignments WHERE id = :assignment_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':assignment_id', $submission['assignment_id']);
$stmt->execute();
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    $_SESSION['error'] = "Assignment not found.";
    header("Location: view_assignments.php");
    exit();
}

if ($assignment['created_by'] != $teacherData['id']) {
    $_SESSION['error'] = "Access denied.";
    header("Location: view_assignments.php");
    exit();
}

try {
    // Update the submission with grade and feedback
    if ($teacher->gradeSubmission($pdo, $submissionId, $grade, $feedback)) {
        $_SESSION['success'] = "Submission graded successfully!";
    } else {
        $_SESSION['error'] = "Failed to grade submission. Please try again.";
    }
} catch (Exception $e) {
    error_log("Grade submission error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while grading the submission.";
}

header("Location: view_submissions.php?assignment_id=" . $assignmentId);
exit();
?>
