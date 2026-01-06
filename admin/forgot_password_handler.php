<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed



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
    $conn = new mysqli("localhost", "root", "", "questportal");
    if ($conn->connect_error) {
        $_SESSION['error'] = "Database connection failed.";
        header('Location: forgot_password.php');
        exit();
    }

    // Check if admin email exists
    $stmt = $conn->prepare("SELECT id FROM staffs WHERE email = ? AND staff_role = 'Admin'");
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
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'makindeayooluwa604@gmail.com';
            $mail->Password = 'lirw zgkb kegs xyat';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('makindeayooluwa604@gmail.com', 'Quest Schools Portal');
            $mail->addAddress($email);

            // Content
            $reset_link = "http://localhost/quest-portal/admin/reset_password.php?token=" . $token . "&type=" . $user_type;
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - Quest Portal Admin';
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>You have requested to reset your admin password for Quest Portal.</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='$reset_link'>Reset Password</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";
            $mail->AltBody = "Reset your password: $reset_link";

            $mail->send();
            $_SESSION['success'] = "Password reset link has been sent to your email.";
        } catch (Exception $e) {
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
?>
