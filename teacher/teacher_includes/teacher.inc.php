<?php

$email = isset($_SESSION["teacher"]) ? $_SESSION["teacher"] : "";

function checkTeacher()
{

    // List pages that should NOT be redirected
    $excluded_pages = ['setup.php','setup_handler.php','forgot_password.php','forgot_password_handler.php','reset_password.php','reset_password_handler.php'];

    $current_page = basename($_SERVER['PHP_SELF']);

    // Skip check for excluded pages
    if (in_array($current_page, $excluded_pages)) {
        return;
    }

    // Redirect all other pages if not logged in
    if (empty($_SESSION["teacher"])) {
        header("Location: login.php");
        exit();
    }
}

checkTeacher();

require_once 'teacher_classes/teacher.class.php';
$teacher = new Teacher($email);


/*      
$teacherData = $teacher->teacherData($pdo, $email);
$students = $teacher->getStudents($pdo);
$staffs = $teacher->getStaffs($pdo);
$classes = $teacher->getClasses($pdo);

$specificStaff = $teacher->getSpecificStaff($pdo, isset($_GET['id']) ? $_GET["id"] : "");

// Enhanced dashboard data fetching
$recentActivities = $teacher->getRecentActivities($pdo, 10);
$notifications = $teacher->getUnreadNotifications($pdo); // Get all notifications for display
$unreadNotifications = is_array($notifications) ? count($notifications) : 0; // Count for badge
$upcomingEvents = $teacher->getUpcomingEvents($pdo, 5);
$systemHealth = $teacher->getSystemHealth($pdo);
$analyticsData = $teacher->getAnalyticsData($pdo);*/