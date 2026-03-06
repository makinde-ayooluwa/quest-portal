<?php
session_start();

include "admin_includes/email_utils.php"; // Adjust path if needed



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $user_type = 'admin'; // Fixed for admin handler

    // Validate input
    if (empty($email)) {
        $_SESSION['error'] = "Please enter your email address.";
        header('Location: forgot_password.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header('Location: forgot_password.php');
        exit();
    }

    // Database connection
    $conn = new mysqli("$host", "root", "", "questportal");
    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed.";
        header('Location: forgot_password.php');
        exit();
    }

    // Check if admin email exists
    $stmt = $conn->prepare("SELECT id FROM staffs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "No admin account found with this email address.";
        $stmt->close();
        $conn->close();
        header('Location: forgot_password.php');
        exit();
    }

    // Generate secure token
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Delete any existing reset tokens for this email and user type
    $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ? AND user_type = ?");
    $delete_stmt->bind_param("ss", $email, $user_type);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Insert new reset token
    $insert_stmt = $conn->prepare("INSERT INTO password_resets (email, user_type, token, expires_at) VALUES (?, ?, ?, ?)");
    $insert_stmt->bind_param("ssss", $email, $user_type, $token, $expires_at);

    if ($insert_stmt->execute()) {
        // Send email
        // $mail = new PHPMailer(true);
        $mail = new EmailUtils($host);
        if ($mail->sendAdminPasswordResetEmail($email, $token, $user_type)) {
            $_SESSION['success'] = "Password reset link has been sent to your email.";
        } else {
            $_SESSION['error'] = "Failed to send email. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Failed to process request. Please try again.";
    }

    $insert_stmt->close();
    $stmt->close();
    $conn->close();

    header('Location: forgot_password.php');
    exit();
} else {
    header('Location: forgot_password.php');
    exit();
}
