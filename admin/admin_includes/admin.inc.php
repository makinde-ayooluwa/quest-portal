<?php
$host = $_SERVER["HTTP_HOST"];
$email = isset($_SESSION["admin"]) ? $_SESSION["admin"] : "";

// auth.php

function checkAdmin()
{

    // List pages that should NOT be redirected
    $excluded_pages = ['setup.php', 'setup_handler.php', 'forgot_password.php', 'forgot_password_handler.php', 'reset_password.php', 'reset_password_handler.php'];

    $current_page = basename($_SERVER['PHP_SELF']);

    // Skip check for excluded pages
    if (in_array($current_page, $excluded_pages)) {
        return;
    }

    // Redirect all other pages if not logged in
    if (empty($_SESSION["admin"])) {
        header("Location: login.php");
        exit();
    }
}

checkAdmin();

require_once 'admin_classes/Admin.class.php';
$admin = new Admin($email);
$adminData = $admin->adminData($pdo, $email);
$students = $admin->getStudents($pdo);
$staffs = $admin->getStaffs($pdo);
$classes = $admin->getClasses($pdo);

$specificStaff = $admin->getSpecificStaff($pdo, isset($_GET['id']) ? $_GET["id"] : "");

// Enhanced dashboard data fetching
$recentActivities = $admin->getRecentActivities($pdo, 10);
$notifications = $admin->getUnreadNotifications($pdo); // Get all notifications for display
$unreadNotifications = is_array($notifications) ? count($notifications) : 0; // Count for badge
$upcomingEvents = $admin->getUpcomingEvents($pdo, 5);
$systemHealth = $admin->getSystemHealth($pdo);
$analyticsData = $admin->getAnalyticsData($pdo);
