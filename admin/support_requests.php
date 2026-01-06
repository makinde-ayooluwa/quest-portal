<?php
session_start();
require_once '../student_includes/autoloader.inc.php';
require_once '../student_includes/db.inc.php';

include "admin_includes/admin.inc.php";

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : null;

// Get support requests
$support_requests = $admin->getAllSupportRequests($pdo, $status_filter, $priority_filter);

// Get stats for dashboard
$support_stats = $admin->getSupportStats($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Support Requests - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        .btn-grad{
            background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
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

        .stats-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--quest-green);
        }

        .filter-section {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            border: 1px solid #dee2e6;
        }

        .table-container {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            border: 1px solid #dee2e6;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
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
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
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

        .btn-view {
            background: linear-gradient(90deg, var(--quest-green) 60%, var(--quest-yellow) 100%);
            color: #fff;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: linear-gradient(90deg, var(--quest-yellow) 60%, var(--quest-green) 100%);
            color: #fff;
            transform: translateY(-1px);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--quest-green);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
            }

            .page-header h1 {
                font-size: 1.75rem;
            }

            .stats-card {
                margin-bottom: 1rem;
                padding: 1rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }

            .filter-section {
                padding: 1rem;
            }

            .filter-section .row > div {
                margin-bottom: 1rem;
            }

            .table-container {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .table th, .table td {
                padding: 0.5rem;
            }

            .btn-view {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .stats-card {
                text-align: center;
            }

            .table th:nth-child(2), .table td:nth-child(2),
            .table th:nth-child(3), .table td:nth-child(3),
            .table th:nth-child(4), .table td:nth-child(4) {
                display: none;
            }

            .table th, .table td {
                padding: 0.25rem;
            }

            .priority-badge, .status-badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php" ?>

    <div class="main-content" style="margin-left: 220px; padding: 20px;">

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
                    <h1 class="display-5 mb-3"><i class="bi bi-headset me-3"></i>Support Requests</h1>
                    <p class="lead mb-0">Manage and respond to student support requests</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['total_requests'] ?? 0; ?></div>
                    <div class="text-muted">Total Requests</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['open_requests'] ?? 0; ?></div>
                    <div class="text-muted">Open</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['in_progress_requests'] ?? 0; ?></div>
                    <div class="text-muted">In Progress</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['closed_requests'] ?? 0; ?></div>
                    <div class="text-muted">Closed</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['urgent_requests'] ?? 0; ?></div>
                    <div class="text-muted">Urgent</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stats-card text-center">
                    <div class="stats-number"><?php echo $support_stats['critical_requests'] ?? 0; ?></div>
                    <div class="text-muted">Critical</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filters</h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="support_requests.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                </div>
            </div>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="open" <?php echo ($status_filter == 'open') ? 'selected' : ''; ?>>Open</option>
                        <option value="in_progress" <?php echo ($status_filter == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="closed" <?php echo ($status_filter == 'closed') ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priority</option>
                        <option value="normal" <?php echo ($priority_filter == 'normal') ? 'selected' : ''; ?>>Normal</option>
                        <option value="urgent" <?php echo ($priority_filter == 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                        <option value="critical" <?php echo ($priority_filter == 'critical') ? 'selected' : ''; ?>>Critical</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-grad">
                        <i class="bi bi-search me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Support Requests Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Support Requests</h5>
                <span class="badge bg-primary"><?php echo count($support_requests); ?> requests</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <!--<th>ID</th>-->
                            <th>Student</th>
                            <th>Topic</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($support_requests)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No support requests found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($support_requests as $request): ?>
                                <tr>
                                    <!--<td><?php //echo htmlspecialchars($request['id']); ?></td>-->
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($request['student_name'] ?? 'Unknown'); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($request['admission_number'] ?? 'N/A'); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['support_topic']); ?></td>
                                    <td><?php echo htmlspecialchars($request['support_subject']); ?></td>
                                    <td>
                                        <span class="priority-badge priority-<?php echo $request['support_priority']; ?>">
                                            <?php echo ucfirst($request['support_priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $request['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($request['added_on'])); ?></td>
                                    <td>
                                        <a href="view_support_request.php?id=<?php echo $request['id']; ?>" class="btn btn-view btn-sm">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                        <a href="javascript:;" onclick="toggleConfirmDelete(<?php echo $request['id'] ?>)" class="btn btn-danger btn-sm">Delete</a>
                                        <script>
                                            function toggleConfirmDelete(id){
                                                if(confirm("Are you sure you want to delete this support request")){
                                                    window.location.href = "delete_support_request.php?id=" + id;
                                                }
                                            }
                                        </script>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>

    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
