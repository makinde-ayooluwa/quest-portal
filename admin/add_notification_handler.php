<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = $_POST['type'];
    // $user_type = $_POST['user_type'];

    if (empty($title) || empty($message) || empty($type)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: add_notification.php");
        exit();
    }

    // Insert notification into database
    $user_id = $adminData['id']; // Get admin's ID
    $query = "INSERT INTO notifications ( user_id, title, message, type) VALUES ( :user_id, :title, :message, :type)";
    $stmt = $pdo->prepare($query);
    // $stmt->bindParam(':user_type', $user_type);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':type', $type);

    if ($stmt->execute()) {
        // Log the activity
        $admin->logActivity($pdo, 'admin', 1, 'Added notification', 'Title: ' . $title);
        $_SESSION['success'] = "Notification added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add notification.";
    }

    header("Location: add_notification.php");
    exit();
} else {
    header("Location: add_notification.php");
    exit();
}
?>
