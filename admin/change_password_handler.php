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

$old = isset($_POST['old_password']) ? $_POST['old_password'] : '';
$new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (empty($old) || empty($new) || empty($confirm)) {
    $_SESSION['error'] = 'All password fields are required.';
    header('Location: profile.php');
    exit();
}

if ($new !== $confirm) {
    $_SESSION['error'] = 'New password and confirm password do not match.';
    header('Location: profile.php');
    exit();
}

// Fetch current password hash for admin
$query = "SELECT pwd FROM staffs WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':email', $adminData['email']);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['error'] = 'Admin account not found.';
    header('Location: profile.php');
    exit();
}


$currentHash = $row['pwd'];

// Support legacy plaintext stored passwords: if stored value looks like a bcrypt hash, use password_verify.
// Otherwise fall back to plaintext comparison (and recommend re-hashing on success).
$isBcrypt = (is_string($currentHash) && (strpos($currentHash, '$2y$') === 0 || strpos($currentHash, '$2a$') === 0 || strpos($currentHash, '$2b$') === 0));
$verified = false;
if ($isBcrypt) {
    $verified = password_verify($old, $currentHash);
} else {
    // legacy: compare plaintext directly
    $verified = ($old === $currentHash);
}

if (!$verified) {
    error_log("Change password failed for admin {$adminData['email']}: provided current password did not match.");
    $_SESSION['error'] = 'Current password is incorrect.';
    header('Location: profile.php');
    exit();
}

$newHash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 10]);

try {
    $update = "UPDATE staffs SET pwd = :pwd WHERE email = :email";
    $uStmt = $pdo->prepare($update);
    $uStmt->bindParam(':pwd', $newHash);
    $uStmt->bindParam(':email', $adminData['email']);
    if ($uStmt->execute()) {
        $_SESSION['success'] = 'Password changed successfully.';
    } else {
        $_SESSION['error'] = 'Failed to change password.';
    }
} catch (PDOException $e) {
    error_log('Change password error: ' . $e->getMessage());
    $_SESSION['error'] = 'Database error occurred.';
}

header('Location: profile.php');
exit();
