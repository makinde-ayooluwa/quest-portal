<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $created_by = $_SESSION['admin']; // Assuming admin email is stored in session

    if (empty($title) || empty($event_date) || empty($location) || empty($start_time) || empty($end_time)) {
        $_SESSION['error'] = "Required fields are missing.";
        header("Location: add_event.php");
        exit();
    }

    // Insert event into database
    $query = "INSERT INTO events (title, description, event_date, start_time, end_time, location, created_by) VALUES (:title, :description, :event_date, :start_time, :end_time, :location, :created_by)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':event_date', $event_date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':created_by', $created_by);

    if ($stmt->execute()) {
        // Log the activity
        $admin->logActivity($pdo, 'admin', 1, 'Added event', 'Title: ' . $title);
        $_SESSION['success'] = "Event added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add event.";
    }

    header("Location: add_event.php");
    exit();
} else {
    header("Location: add_event.php");
    exit();
}
