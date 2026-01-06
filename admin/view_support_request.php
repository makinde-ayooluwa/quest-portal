<?php
session_start();
require_once '../student_includes/autoloader.inc.php';
require_once '../student_includes/db.inc.php';

include "admin_includes/admin.inc.php";

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: support_requests.php");
    exit();
}

$request_id = (int)$_GET['id'];
$support_request = $admin->getSupportRequestById($pdo, $request_id);

if (!$support_request) {
    $_SESSION["error"] = "Support request not found.";
    header("Location: support_requests.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Support Request - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .request-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: none;
            width: 100%;
            box-sizing: border-box;
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-open {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-closed {
            background-color: #d4edda;
            color: #155724;
        }

        .priority-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .priority-normal {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .priority-urgent {
            background-color: #f8d7da;
            color: #721c24;
        }

        .priority-critical {
            background-color: #f5c6cb;
            color: #721c24;
        }

        .info-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .response-section {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid #dee2e6;
        }

        .btn-grad {
            background: linear-gradient(90deg, var(--quest-green) 60%, var(--quest-yellow) 100%);
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, var(--quest-yellow) 60%, var(--quest-green) 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .form-control:focus {
            border-color: var(--quest-green);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--quest-green);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--quest-green);
            border: 3px solid #fff;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .request-card {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .response-section {
                padding: 1.5rem;
            }

            .info-section {
                padding: 1rem;
            }

            .btn-grad {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 10px;
                overflow-x: hidden;
            }

            .row {
                --bs-gutter-x: 0;
            }

            .col-lg-8,
            .col-lg-4 {
                padding: 0 0.5rem;
            }

            .container-fluid {
                padding-left: 0;
                padding-right: 0;
            }
        }

        @media (max-width: 576px) {
            .request-card {
                padding: 1rem;
            }

            .response-section {
                padding: 1rem;
            }

            .info-section {
                padding: 0.75rem;
            }

            .d-flex.justify-content-between.align-items-start {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .text-end {
                text-align: left !important;
                margin-top: 1rem;
            }

            .timeline-item {
                padding-left: 1rem;
            }

            .timeline-item::before {
                left: -1rem;
            }
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php" ?>

    <div class="main-content" style="margin-left: 220px; padding: 20px; transition: margin-left 0.3s ease;">

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION["error"])): ?>
            <script>
                toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
            </script>
            <?php unset($_SESSION["error"]); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION["success"])): ?>
            <script>
                toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
            </script>
            <?php unset($_SESSION["success"]); ?>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1 class="display-5 mb-3"><i class="bi bi-eye me-3"></i>Support Request #<?php echo $support_request['id']; ?></h1>
                        <p class="lead mb-0">View and respond to support request</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Request Details -->
                    <div class="request-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="mb-2"><?php echo htmlspecialchars($support_request['support_subject']); ?></h3>
                                <p class="text-muted mb-0">Topic: <?php echo htmlspecialchars($support_request['support_topic']); ?></p>
                            </div>
                            <div class="text-end">
                                <span class="status-badge status-<?php echo $support_request['status']; ?> mb-2">
                                    <?php echo ucfirst(str_replace('_', ' ', $support_request['status'])); ?>
                                </span>
                                <br>
                                <span class="priority-badge priority-<?php echo $support_request['support_priority']; ?>">
                                    <?php echo ucfirst($support_request['support_priority']); ?> Priority
                                </span>
                            </div>
                        </div>

                        <!-- Student Information -->
                        <div class="info-section">
                            <h5 class="mb-3"><i class="bi bi-person me-2"></i>Student Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($support_request['student_name'] ?? 'Unknown'); ?></p>
                                    <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($support_request['admission_number'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Class:</strong> <?php echo htmlspecialchars($support_request['class'] ?? 'N/A'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($support_request['email']); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($support_request['phone'])): ?>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($support_request['phone']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Request Details -->
                        <div class="info-section">
                            <h5 class="mb-3"><i class="bi bi-chat-quote me-2"></i>Request Details</h5>
                            <p><strong>Submitted:</strong> <?php echo date('F d, Y \a\t H:i', strtotime($support_request['added_on'])); ?></p>
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    <?php echo nl2br(htmlspecialchars($support_request['support_description'])); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Response Section -->
                        <?php if (!empty($support_request['admin_response'])): ?>
                            <div class="info-section">
                                <h5 class="mb-3"><i class="bi bi-reply me-2"></i>Admin Response</h5>
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="">
                                            <div>
                                                <strong><?php echo htmlspecialchars($support_request['responded_by_name'] ?? 'Admin'); ?></strong>
                                                <p class="text-muted mb-2"><?php echo date('F d, Y \a\t H:i', strtotime($support_request['responded_at'])); ?></p>
                                                <div class="p-3 bg-white rounded border">
                                                    <?php echo nl2br($support_request['admin_response']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Quick Actions -->
                    <div class="request-card">
                        <h5 class="mb-4"><i class="bi bi-gear me-2"></i>Quick Actions</h5>

                        <!-- Status Update -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Update Status</label>
                            <form action="support_response_handler.php" method="post">
                                <input type="hidden" name="request_id" value="<?php echo $support_request['id']; ?>">
                                <input type="hidden" name="action" value="update_status">
                                <div class="mb-3">
                                    <select class="form-select" name="status" required>
                                        <option value="open" <?php echo ($support_request['status'] == 'open') ? 'selected' : ''; ?>>Open</option>
                                        <option value="in_progress" <?php echo ($support_request['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="closed" <?php echo ($support_request['status'] == 'closed') ? 'selected' : ''; ?>>Closed</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-grad w-100 mb-3">
                                    <i class="bi bi-check-circle me-1"></i>Update Status
                                </button>
                            </form>
                        </div>

                        <!-- Response Form -->
                        <div class="response-section">
                            <h6 class="mb-3"><i class="bi bi-reply me-2"></i>Send Response</h6>
                            <form action="support_response_handler.php" method="post">
                                <input type="hidden" name="request_id" value="<?php echo $support_request['id']; ?>">
                                <input type="hidden" name="action" value="respond">
                                <div class="mb-3">
                                    <label for="response" class="form-label">Your Response</label>
                                    <textarea class="form-control" id="response" name="response" rows="6" placeholder="Type your response here..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Update Status After Response</label>
                                    <select class="form-select" name="status_after_response">
                                        <option value="">Keep Current Status</option>
                                        <option value="in_progress">Mark as In Progress</option>
                                        <option value="closed">Mark as Closed</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-grad w-100">
                                    <i class="bi bi-send me-1"></i>Send Response
                                </button>
                            </form>
                        </div>

                        <!-- Back Button -->
                        <div class="mt-4">
                            <a href="support_requests.php" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left me-1"></i>Back to Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            // Form validation
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const textareas = form.querySelectorAll('textarea[required]');
                    textareas.forEach(textarea => {
                        if (!textarea.value.trim()) {
                            e.preventDefault();
                            alert('Please fill in all required fields.');
                            return false;
                        }
                    });
                    textareas.forEach(textarea => {
                        textarea.addEventListener("keydown", function(e) {
                            if (e.key === "Enter") {
                                e.preventDefault(); // prevent default newline behavior
                                const start = this.selectionStart;
                                const end = this.selectionEnd;

                                // Insert <br /> at the cursor position
                                this.value = this.value.substring(0, start) + "<br />" + this.value.substring(end);

                                // Move cursor after the inserted text
                                this.selectionStart = this.selectionEnd = start + 6;
                            }
                        });
                    });

                });
            });
        </script>
</body>

</html>