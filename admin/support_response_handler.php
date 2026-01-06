<?php
session_start();
require_once '../student_includes/autoloader.inc.php';
require_once '../student_includes/db.inc.php';

include "admin_includes/admin.inc.php";
include "admin_includes/email_utils.php";

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get admin data for logging
$adminData = $admin->adminData($pdo, $_SESSION['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: support_requests.php");
    exit();
}

$action = $_POST['action'] ?? '';
$request_id = (int)($_POST['request_id'] ?? 0);

if (!$request_id) {
    $_SESSION["error"] = "Invalid request ID.";
    header("Location: support_requests.php");
    exit();
}

// Verify the request exists
$support_request = $admin->getSupportRequestById($pdo, $request_id);
if (!$support_request) {
    $_SESSION["error"] = "Support request not found.";
    header("Location: support_requests.php");
    exit();
}

try {
    if ($action === 'update_status') {
        $new_status = $_POST['status'] ?? '';

        if (!in_array($new_status, ['open', 'in_progress', 'closed'])) {
            $_SESSION["error"] = "Invalid status.";
            header("Location: view_support_request.php?id=$request_id");
            exit();
        }

        // Update status
        $result = $admin->updateSupportRequestStatus($pdo, $request_id, $new_status);

        if ($result) {
            // Log activity
            $admin->logActivity($pdo, 'admin', $adminData['id'], 'Updated support request status',
                "Request #$request_id status changed to " . ucfirst(str_replace('_', ' ', $new_status)));

            $_SESSION["success"] = "Support request status updated successfully.";
        } else {
            $_SESSION["error"] = "Failed to update status.";
        }

    } elseif ($action === 'respond') {
        $response = trim($_POST['response']);
        $status_after_response = $_POST['status_after_response'] ?? '';

        if (empty($response)) {
            $_SESSION["error"] = "Response cannot be empty.";
            header("Location: view_support_request.php?id=$request_id");
            exit();
        }

        // Update with response
        $result = $admin->updateSupportRequestStatus(
            $pdo,
            $request_id,
            $status_after_response ?: $support_request['status'],
            $response,
            $adminData['id']
        );

        if ($result) {
            // Log activity
            $admin->logActivity($pdo, 'admin', $adminData['id'], 'Responded to support request',
                "Responded to request #$request_id");

            // Send email notification to student
            $emailUtils = new EmailUtils();
            $studentName = $support_request['student_name'] ?? 'Student';
            $emailSent = $emailUtils->sendSupportResponseEmail(
                $support_request['email'],
                $studentName,
                $support_request['support_subject'],
                $response
            );

            if (!$emailSent) {
                // Log email failure but don't fail the response
                error_log("Failed to send support response email to: " . $support_request['email']);
            }

            // Do not create notification for support request response

            $_SESSION["success"] = "Response sent successfully.";
        } else {
            $_SESSION["error"] = "Failed to send response.";
        }

    } else {
        $_SESSION["error"] = "Invalid action.";
    }

} catch (Exception $e) {
    $_SESSION["error"] = "An error occurred: " . $e->getMessage();
}

header("Location: view_support_request.php?id=$request_id");
exit();
?>
