<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Fetch assignments for the current student's class
$query = "SELECT a.*, 
          CASE WHEN asub.submitted_at IS NOT NULL THEN 'submitted' 
               WHEN CURDATE() > a.due_date THEN 'overdue' 
               ELSE 'pending' END as status,
          asub.submitted_at,
          asub.grade,
          asub.feedback
          FROM assignments a 
          LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
          WHERE a.class_name = :class_name 
          ORDER BY a.due_date ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":student_id", $studentData['id']);
$stmt->bindParam(":class_name", $studentData['class']);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Quest Schools</title>
    <!-- Bootstrap CSS -->
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/Quest logo.jpg" type="image/x-icon">
    <!--Styles-->
    <link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!--Scripts-->
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .assign-card {
            max-width: 1200px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .assignment-row {
            transition: all 0.3s ease;
        }

        .assignment-row:hover {
            background-color: #f8f9fa;
        }

        .download-btn {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
        }

        .download-btn:hover {
            background: linear-gradient(90deg, #198754 60%, #0d6efd 100%);
            color: #fff;
        }

        .upload-btn {
            background: linear-gradient(90deg, #5aac7b, #fec511);
            color: #fff;
            border: none;
        }

        .upload-btn:hover {
            background: linear-gradient(90deg, #fec511, #5aac7b);
            color: #fff;
        }

        .status-badge {
            font-size: 0.8rem;
        }

        .overdue {
            background-color: #dc3545 !important;
        }

        .submitted {
            background-color: #198754 !important;
        }

        .pending {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .graded {
            background-color: #6f42c1 !important;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-lg-9">
                <div class="assign-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">My Assignments</h2>
                        <a href="assignment_upload.php" class="btn upload-btn">
                            <i class="bi bi-upload me-1"></i>Upload Assignment
                        </a>
                    </div>

                    <?php if (empty($assignments)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                            <h4>No assignments yet</h4>
                            <p class="text-muted">Your assignments will appear here when they are assigned.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Subject</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Grade</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr class="assignment-row">
                                            <td class="fw-bold"><?php echo htmlspecialchars($assignment['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                            <td>
                                                <?php
                                                $description = $assignment['description'];
                                                echo strlen($description) > 50 ? htmlspecialchars(substr($description, 0, 50)) . '...' : htmlspecialchars($description);
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $dueDate = new DateTime($assignment['due_date']);
                                                $today = new DateTime();
                                                $isOverdue = $today > $dueDate && $assignment['status'] !== 'submitted';
                                                ?>
                                                <span class="<?php echo $isOverdue ? 'text-danger fw-bold' : ''; ?>">
                                                    <?php echo $dueDate->format('M j, Y'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $assignment['status'];
                                                $badgeClass = 'pending';
                                                if ($status === 'submitted') {
                                                    $badgeClass = 'submitted';
                                                } elseif ($status === 'overdue') {
                                                    $badgeClass = 'overdue';
                                                } elseif (!empty($assignment['grade'])) {
                                                    $badgeClass = 'graded';
                                                    $status = 'Graded';
                                                }
                                                ?>
                                                <span class="badge status-badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($assignment['grade'])): ?>
                                                    <span class="fw-bold text-primary"><?php echo htmlspecialchars($assignment['grade']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if (!empty($assignment['file_path'])): ?>
                                                        <a href="<?php echo htmlspecialchars("teacher/" . $assignment['file_path']); ?>"
                                                           download class="btn download-btn btn-sm">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($assignment['status'] === 'submitted' && !empty($assignment['feedback'])): ?>
                                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#feedbackModal<?php echo $assignment['id']; ?>">
                                                            <i class="bi bi-chat-dots"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Feedback Modal -->
                                        <?php if ($assignment['status'] === 'submitted' && !empty($assignment['feedback'])): ?>
                                            <div class="modal fade" id="feedbackModal<?php echo $assignment['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Feedback for <?php echo htmlspecialchars($assignment['title']); ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><?php echo nl2br(htmlspecialchars($assignment['feedback'])); ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
