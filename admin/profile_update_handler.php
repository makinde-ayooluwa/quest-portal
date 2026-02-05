<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: profile.php');
    exit();
}

$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if (empty($fullname)) {
    $_SESSION['error'] = 'Full name is required.';
    header('Location: profile.php');
    exit();
}

// Ensure we have admin data
$picturePath = $adminData['picture'] ?? '';
if (empty($adminData) || empty($adminData['email'])) {
    $_SESSION['error'] = 'Admin session not found. Please login again.';
    header('Location: profile.php');
    exit();
}

// Handle picture upload if provided
if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['picture']['tmp_name'];
    $name = basename($_FILES['picture']['name']);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) {
        $_SESSION['error'] = 'Invalid image type. Allowed: jpg, jpeg, png, webp.';
        header('Location: profile.php');
        exit();
    }
    // Limit to 3MB
    if ($_FILES['picture']['size'] > 3 * 1024 * 1024) {
        $_SESSION['error'] = 'Image too large. Max 3MB.';
        header('Location: profile.php');
        exit();
    }

    // Use admin assets folder
    $targetDir = 'assets/images/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $newName = 'admin_' . time() . '_' . rand(1000,9999) . '.' . $ext;
    $dest = $targetDir . $newName;

    if (move_uploaded_file($tmp, $dest)) {
        // Web-visible path
        $picturePath = 'assets/images/' . $newName;
    } else {
        $_SESSION['error'] = 'Failed to upload image.';
        header('Location: profile.php');
        exit();
    }
}

try {
    $query = "UPDATE staffs SET fullname = :fullname, phone = :phone, picture = :picture WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':picture', $picturePath);
    $stmt->bindParam(':email', $adminData['email']);
    $executed = $stmt->execute();
    if ($executed) {
        // check rows affected
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Profile updated successfully.';
        } else {
            // No rows updated - maybe data is identical
            $_SESSION['info'] = 'No changes detected or update not required.';
        }
    } else {
        $_SESSION['error'] = 'Failed to update profile.';
    }
} catch (PDOException $e) {
    error_log('Profile update error: ' . $e->getMessage());
    $_SESSION['error'] = 'Database error occurred.';
}

header('Location: profile.php');
exit();
