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
    <title>Assignments - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        .assign-card {
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            animation: fadeInUp 0.4s ease forwards;
        }

        .assignment-row {
            transition: all var(--transition-base);
        }

        .assignment-row:hover {
            background-color: var(--slate-50);
        }

        .download-btn {
            background: linear-gradient(135deg, var(--sky-500), var(--quest-green));
            color: #fff;
            border: none;
            transition: all var(--transition-base);
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .upload-btn {
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff;
            border: none;
            transition: all var(--transition-base);
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-green);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
            border-radius: var(--radius-full);
            font-weight: 600;
        }

        .overdue {
            background: #fee2e2 !important;
            color: #991b1b !important;
        }

        .submitted {
            background: #d1fae5 !important;
            color: #065f46 !important;
        }

        .pending {
            background: var(--quest-yellow-100) !important;
            color: var(--quest-yellow-600) !important;
        }

        .graded {
            background: #ede9fe !important;
            color: #6d28d9 !important;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
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
