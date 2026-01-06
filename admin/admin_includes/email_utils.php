<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../vendor/autoload.php';

class EmailUtils {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);

        // SMTP configuration
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'makindeayooluwa604@gmail.com';
        $this->mail->Password = 'lirw zgkb kegs xyat'; // App password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
        $this->mail->setFrom('makindeayooluwa604@gmail.com', 'Quest Schools Portal');
        $this->mail->isHTML(true);
    }

    public function sendStudentSetupEmail($studentEmail, $studentName, $admissionNumber) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($studentEmail, $studentName);

            $this->mail->Subject = 'Quest Schools - Student Portal Setup';
            $setupLink = "http://localhost/quest-portal/setup.php?admission_number=" . urlencode($admissionNumber);

            $this->mail->Body = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">
                <img src=\"http://localhost/quest-portal/assets/images/quest.jpg\" alt=\"Quest Schools Logo\" style=\"width: 100%; max-width: 200px; margin: 20px auto; display: block;\">
                <div style=\"background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">
                    <h2 style=\"color: #333; text-align: center;\">Welcome to Quest Schools!</h2>
                    <p style=\"font-size: 16px; line-height: 1.6; color: #555;\">
                        Hi " . htmlspecialchars($studentName) . ",<br><br>
                        You have been successfully registered as a student at The Quest Schools.<br>
                        Your student portal can now be activated by clicking the link below.
                    </p>
                    <div style=\"text-align: center; margin: 30px 0;\">
                        <a href=\"$setupLink\" style=\"background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;\">Setup Your Portal</a>
                    </div>
                    <p style=\"font-size: 14px; color: #777; text-align: center;\">
                        If the button doesn't work, copy and paste this link into your browser:<br>
                        <a href=\"$setupLink\">$setupLink</a>
                    </p>
                </div>
                <div style=\"text-align: center; margin-top: 20px; font-size: 12px; color: #999;\">
                    <p>&copy; 2024 Quest Schools. All rights reserved.</p>
                </div>
            </div>";

            $this->mail->AltBody = 'Hi ' . $studentName . ', You have been registered at Quest Schools. Setup your portal here: ' . $setupLink;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Student email failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendStaffSetupEmail($staffEmail, $staffName, $portalCode, $staffRole) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($staffEmail, $staffName);

            if (strtolower($staffRole) === 'admin') {
                $this->mail->Subject = 'Quest Schools - Admin Portal Setup';
                $setupLink = "http://localhost/quest-portal/admin/setup.php?portal_code=" . urlencode($portalCode);
                $roleMessage = 'as an Administrator';
            } elseif(strtolower($staffRole) === 'teacher') {
                $this->mail->Subject = 'Quest Schools - Teacher Portal Setup';
                $setupLink = "http://localhost/quest-portal/teacher/setup.php?portal_code=" . urlencode($portalCode);
                $roleMessage = 'as Teacher (' . $staffRole . ')';
            }

            $this->mail->Body = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">
                <img src=\"http://localhost/quest-portal/assets/images/quest.jpg\" alt=\"Quest Schools Logo\" style=\"width: 100%; max-width: 200px; margin: 20px auto; display: block;\">
                <div style=\"background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">
                    <h2 style=\"color: #333; text-align: center;\">Welcome to Quest Schools!</h2>
                    <p style=\"font-size: 16px; line-height: 1.6; color: #555;\">
                        Hi " . htmlspecialchars($staffName) . ",<br><br>
                        You have been successfully registered $roleMessage at The Quest Schools.<br>
                        Your portal can now be activated by clicking the link below.
                    </p>
                    <div style=\"text-align: center; margin: 30px 0;\">
                        <a href=\"$setupLink\" style=\"background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;\">Setup Your Portal</a>
                    </div>
                    <p style=\"font-size: 14px; color: #777; text-align: center;\">
                        If the button doesn't work, copy and paste this link into your browser:<br>
                        <a href=\"$setupLink\">$setupLink</a>
                    </p>
                </div>
                <div style=\"text-align: center; margin-top: 20px; font-size: 12px; color: #999;\">
                    <p>&copy; 2024 Quest Schools. All rights reserved.</p>
                </div>
            </div>";

            $this->mail->AltBody = 'Hi ' . $staffName . ', You have been registered ' . $roleMessage . ' at Quest Schools. Setup your portal here: ' . $setupLink;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Staff email failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendSupportResponseEmail($studentEmail, $studentName, $requestSubject, $adminResponse) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($studentEmail, $studentName);

            $this->mail->Subject = 'Quest Schools - Support Request Response';

            $this->mail->Body = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">
                <img src=\"http://localhost/quest-portal/assets/images/quest.jpg\" alt=\"Quest Schools Logo\" style=\"width: 100%; max-width: 200px; margin: 20px auto; display: block;\">
                <div style=\"background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">
                    <h2 style=\"color: #333; text-align: center;\">Support Request Response</h2>
                    <p style=\"font-size: 16px; line-height: 1.6; color: #555;\">
                        Hi " . htmlspecialchars($studentName) . ",<br><br>
                        We have responded to your support request regarding: <strong>" . htmlspecialchars($requestSubject) . "</strong>
                    </p>
                    <div style=\"background-color: #fff; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff; margin: 20px 0;\">
                        <h5 style=\"margin-top: 0; color: #007bff;\">Admin Response:</h5>
                        <div style=\"color: #333; line-height: 1.6;\">
                            " . nl2br(htmlspecialchars($adminResponse)) . "
                        </div>
                    </div>
                    <p style=\"font-size: 14px; color: #777;\">
                        If you have any further questions or need additional assistance, please don't hesitate to submit another support request.
                    </p>
                    <div style=\"text-align: center; margin: 30px 0;\">
                        <a href=\"http://localhost/quest-portal/support.php\" style=\"background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;\">Submit New Request</a>
                    </div>
                </div>
                <div style=\"text-align: center; margin-top: 20px; font-size: 12px; color: #999;\">
                    <p>&copy; 2024 Quest Schools. All rights reserved.</p>
                </div>
            </div>";

            $this->mail->AltBody = 'Hi ' . $studentName . ', We have responded to your support request: ' . $requestSubject . '. Response: ' . $adminResponse;

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Support response email failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function sendPromotionEmail($staffEmail, $staffName) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($staffEmail, $staffName);

            $this->mail->Subject = 'Quest Schools - Congratulations on Your Promotion!';

            $this->mail->Body = "
            <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\">
                <img src=\"http://localhost/quest-portal/assets/images/quest.jpg\" alt=\"Quest Schools Logo\" style=\"width: 100%; max-width: 200px; margin: 20px auto; display: block;\">
                <div style=\"background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;\">
                    <h2 style=\"color: #333; text-align: center;\">Congratulations on Your Promotion!</h2>
                    <p style=\"font-size: 16px; line-height: 1.6; color: #555;\">
                        Hi " . htmlspecialchars($staffName) . ",<br><br>
                        Congratulations! You have been successfully promoted to the role of <strong>Administrator</strong> at The Quest Schools.<br><br>
                        As an Administrator, you will have access to enhanced features and responsibilities within the portal. Your new role includes managing staff, students, classes, and overseeing the overall administration of the school system.
                    </p>
                    <div style=\"background-color: #fff; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;\">
                        <h5 style=\"margin-top: 0; color: #28a745;\">What You Can Do Now:</h5>
                        <ul style=\"color: #333; line-height: 1.6;\">
                            <li>Manage staff members and their roles</li>
                            <li>Oversee student registrations and profiles</li>
                            <li>Create and manage classes</li>
                            <li>Access advanced reporting and analytics</li>
                            <li>Handle support requests and system administration</li>
                        </ul>
                    </div>
                    <p style=\"font-size: 14px; color: #777;\">
                        If you have any questions about your new responsibilities or need assistance getting started, please don't hesitate to reach out to the other administrators.
                    </p>
                    <p style=\"font-size: 14px; color: #777;\">
                        Login to your new admin portal with your previous staff credentials.
                    </p>
                    <div style=\"text-align: center; margin: 30px 0;\">
                        <a href=\"http://localhost/quest-portal/admin/\" style=\"background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;\">Access Admin Portal</a>
                    </div>
                </div>
                <div style=\"text-align: center; margin-top: 20px; font-size: 12px; color: #999;\">
                    <p>&copy; 2024 Quest Schools. All rights reserved.</p>
                </div>
            </div>";

            $this->mail->AltBody = 'Hi ' . $staffName . ', Congratulations! You have been promoted to Administrator at Quest Schools. You now have access to enhanced administrative features.';

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Promotion email failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
