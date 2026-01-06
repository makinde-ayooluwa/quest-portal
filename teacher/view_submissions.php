<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['assignment_id'])) {
    header("Location: view_assignments.php");
    exit();
}

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$assignmentId = (int)$_GET['assignment_id'];

// Verify the assignment belongs to this teacher and get assignment details
$query = "SELECT * FROM assignments WHERE id = :id AND created_by = :teacher_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $assignmentId);
$stmt->bindParam(':teacher_id', $teacherData['id']);
$stmt->execute();
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assignment) {
    header("Location: view_assignments.php");
    exit();
}

// Handle grading submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grade_submission'])) {
    $submissionId = $_POST['submission_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    if ($teacher->gradeSubmission($pdo, $submissionId, $grade, $feedback)) {
        $_SESSION['success'] = "Submission graded successfully!";
    } else {
        $_SESSION['error'] = "Failed to grade submission.";
    }
    header("Location: view_submissions.php?assignment_id=" . $assignmentId);
    exit();
}

$submissions = $teacher->getSubmittedAssignments($pdo, $assignmentId);
$dueDate = new DateTime($assignment['due_date']);
$today = new DateTime();
$isOverdue = $today > $dueDate;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Submissions - <?php echo htmlspecialchars($assignment['title']); ?> - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .submissions-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
        }

        .assignment-header {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .assignment-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .assignment-meta {
            display: flex;
            gap: 2rem;
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .submission-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .submission-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .student-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .student-name {
            font-weight: 600;
            color: #495057;
            font-size: 1.1rem;
        }

        .submission-date {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .download-btn {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: linear-gradient(90deg, #198754 60%, #0d6efd 100%);
            color: #fff;
            transform: translateY(-1px);
        }

        .grade-form {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid #dee2e6;
        }

        .hidden {
            display: none !important;
        }

        .graded-badge {
            background-color: #198754 !important;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .pending-badge {
            background-color: #ffc107 !important;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-grade {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            border: none;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 5px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-grade:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
        }

        .stats-number {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--quest-green);
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php"; ?>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>toastr.error('" . $_SESSION['error'] . "', 'Error!');</script>";
        unset($_SESSION['error']);
    } elseif (isset($_SESSION["success"])) {
        echo "<script>toastr.success('" . $_SESSION['success'] . "', 'Success!');</script>";
        unset($_SESSION['success']);
    }
    ?>

    <main class="main-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Assignment Submissions</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="view_assignments.php">My Assignments</a></li>
                            <li class="breadcrumb-item active">Submissions</li>
                        </ol>
                    </nav>
                </div>
                <a href="view_assignments.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                </a>
            </div>

            <!-- Assignment Header -->
            <div class="assignment-header">
                <h3 class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                <div class="assignment-meta">
                    <span><strong>Subject:</strong> <?php echo htmlspecialchars($assignment['subject']); ?></span>
                    <span><strong>Class:</strong> <?php echo htmlspecialchars($assignment['class_name']); ?></span>
                    <span><strong>Due:</strong> <?php echo $dueDate->format('M j, Y'); ?> <?php if ($isOverdue): ?><span class="text-warning">(Overdue)</span><?php endif; ?></span>
                    <span><strong>Posted:</strong> <?php echo date('M j, Y', strtotime($assignment['created_at'])); ?></span>
                </div>
                <?php if (!empty($assignment['description'])): ?>
                    <p class="mt-3 mb-0"><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                <?php endif; ?>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?php echo count($submissions); ?></div>
                        <div class="text-muted">Total Submissions</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number">
                            <?php echo count(array_filter($submissions, function($s) { return !empty($s['grade']); })); ?>
                        </div>
                        <div class="text-muted">Graded</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number">
                            <?php echo count(array_filter($submissions, function($s) { return empty($s['grade']); })); ?>
                        </div>
                        <div class="text-muted">Pending Grade</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <div class="stats-number">
                            <?php
                            $lateSubmissions = count(array_filter($submissions, function($s) use ($dueDate) {
                                return new DateTime($s['submitted_at']) > $dueDate;
                            }));
                            echo $lateSubmissions;
                            ?>
                        </div>
                        <div class="text-muted">Late Submissions</div>
                    </div>
                </div>
            </div>

            <div class="submissions-container">
                <?php if (empty($submissions)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h4>No submissions yet</h4>
                        <p class="text-muted">Students haven't submitted this assignment yet.</p>
                    </div>
                <?php else: ?>
                    <h4 class="mb-3">Student Submissions</h4>
                    <?php foreach ($submissions as $submission): ?>
                        <div class="submission-item">
                            <div class="student-info">
                                <div>
                                    <div class="student-name"><?php echo htmlspecialchars($submission['fullname']); ?></div>
                                    <div class="text-muted small">Admission No: <?php echo htmlspecialchars($submission['admission_number']); ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="submission-date">
                                        Submitted: <?php echo date('M j, Y g:i A', strtotime($submission['submitted_at'])); ?>
                                        <?php if (new DateTime($submission['submitted_at']) > $dueDate): ?>
                                            <span class="text-danger small">(Late)</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($submission['grade'])): ?>
                                        <span class="badge graded-badge">Graded: <?php echo htmlspecialchars($submission['grade']); ?></span>
                                    <?php else: ?>
                                        <span class="badge pending-badge">Pending Grade</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if (!empty($submission['comments'])): ?>
                                <div class="mb-3">
                                    <strong>Student Comments:</strong>
                                    <p class="text-muted mb-2"><?php echo nl2br(htmlspecialchars($submission['comments'])); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="uploads/assignments/<?php echo htmlspecialchars($submission['submission_file']); ?>"
                                       download class="btn download-btn">
                                        <i class="bi bi-download me-1"></i>Download Submission
                                    </a>
                                </div>

                                <?php if (empty($submission['grade'])): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="grade_submission" value="1">
                                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignmentId; ?>">
                                        <input type="text" name="grade" placeholder="Grade" required class="form-control form-control-sm d-inline" style="width: 80px;">
                                        <textarea name="feedback" placeholder="Feedback" rows="1" class="form-control form-control-sm d-inline" style="width: 150px;"></textarea>
                                        <button type="submit" class="btn btn-grade btn-sm">
                                            <i class="bi bi-pencil me-1"></i>Grade
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="text-end">
                                        <small class="text-muted">Grade: <strong><?php echo htmlspecialchars($submission['grade']); ?></strong></small>
                                        <?php if (!empty($submission['feedback'])): ?>
                                            <br><small class="text-muted">Feedback provided</small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>


                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Grade Submission Modal -->
    <div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeModalLabel">Grade Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="gradeForm" action="grade_submission_handler.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="grade_submission" value="1">
                        <input type="hidden" name="submission_id" id="modalSubmissionId">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignmentId; ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Grade <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="grade" placeholder="e.g., A+, 95%" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Feedback (optional)</label>
                                <textarea class="form-control" name="feedback" rows="3" placeholder="Provide constructive feedback..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Submit Grade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggler functionality
        const header = document.querySelector('header');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        sidebar.style.height = `calc(100vh - ${header.offsetHeight}px)`;
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Optional: Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Set submission ID when modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            const gradeModal = document.getElementById('gradeModal');
            gradeModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const submissionId = button.getAttribute('data-submission-id');
                document.getElementById('modalSubmissionId').value = submissionId;
            });

            // Reset form when modal is closed
            gradeModal.addEventListener('hidden.bs.modal', function () {
                const form = gradeModal.querySelector('form');
                if (form) {
                    form.reset();
                }
            });
        });


    </script>
</body>

</html>
