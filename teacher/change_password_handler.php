<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_SESSION['teacher'];

    // Validate passwords
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'New passwords do not match.';
        header('Location: profile.php');
        exit();
    }

    if (strlen($newPassword) < 6) {
        $_SESSION['error'] = 'New password must be at least 6 characters long.';
        header('Location: profile.php');
        exit();
    }

    // Get current password hash
    $stmt = $pdo->prepare("SELECT pwd FROM staffs WHERE email = :email AND staff_role = 'Teacher'");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($oldPassword, $user['pwd'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: profile.php');
        exit();
    }

    // Update password
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE staffs SET pwd = :pwd WHERE email = :email AND staff_role = 'Teacher'");
    $updateStmt->execute([':pwd' => $newHash, ':email' => $email]);

    $_SESSION['success'] = 'Password changed successfully!';
    header('Location: profile.php');
    exit();
}
?>
