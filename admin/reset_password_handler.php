<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token']);
    $user_type = trim($_POST['user_type']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($token) || empty($user_type) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header('Location: reset_password.php?token=' . urlencode($token) . '&type=' . urlencode($user_type));
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header('Location: reset_password.php?token=' . urlencode($token) . '&type=' . urlencode($user_type));
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
        header('Location: reset_password.php?token=' . urlencode($token) . '&type=' . urlencode($user_type));
        exit();
    }

    if ($user_type !== 'admin') {
        $_SESSION['error'] = "Invalid user type.";
        header('Location: login.php');
        exit();
    }

    // Database connection
    $conn = new mysqli("localhost", "root", "", "questportal");
    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed.";
        header('Location: reset_password.php?token=' . urlencode($token) . '&type=' . urlencode($user_type));
        exit();
    }

    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND user_type = ? AND expires_at > NOW()");
    $stmt->bind_param("ss", $token, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Invalid or expired reset link.";
        $stmt->close();
        $conn->close();
        header('Location: login.php');
        exit();
    }

    $row = $result->fetch_assoc();
    $email = $row['email'];

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update password in staffs table for admin
    $update_stmt = $conn->prepare("UPDATE staffs SET pwd = ? WHERE email = ? AND staff_role = 'Admin'");
    $update_stmt->bind_param("ss", $hashed_password, $email);

    if ($update_stmt->execute()) {
        // Delete the used token
        $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ? AND user_type = ?");
        $delete_stmt->bind_param("ss", $token, $user_type);
        $delete_stmt->execute();
        $delete_stmt->close();

        $_SESSION['success'] = "Password has been reset successfully. You can now log in with your new password.";
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to reset password. Please try again.";
        header('Location: reset_password.php?token=' . urlencode($token) . '&type=' . urlencode($user_type));
        exit();
    }

    //$update_stmt->close();
    //$stmt->close();
    //$conn->close();
} else {
    header('Location: login.php');
    exit();
}
?>
