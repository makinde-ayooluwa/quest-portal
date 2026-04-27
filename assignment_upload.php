<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Fetch available assignments for the student's class that haven't been submitted yet
$query = "SELECT a.id, a.title, a.subject, a.due_date
          FROM assignments a
          LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
          WHERE a.class_name = :class_name AND asub.submitted_at IS NULL
          ORDER BY a.due_date ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":student_id", $studentData['id']);
$stmt->bindParam(":class_name", $studentData['class']);
$stmt->execute();
$availableAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Upload Assignment - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        .upload-card {
            max-width: 600px;
            margin: 3rem auto;
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            animation: fadeInUp 0.4s ease forwards;
        }

        .btn-grad {
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff;
            border: none;
            transition: all var(--transition-base);
        }

        .btn-grad:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-green);
        }

        .assignment-option {
            border: 2px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all var(--transition-base);
        }

        .assignment-option:hover {
            background-color: var(--slate-50);
            border-color: var(--quest-green);
        }

        .assignment-option.selected {
            background-color: var(--quest-green-50);
            border-color: var(--quest-green);
        }

        .due-date-warning {
            color: var(--rose-500);
            font-weight: bold;
        }

        .due-date-normal {
            color: var(--slate-500);
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
                <div class="upload-card">
                    <h2 class="mb-4 text-center"><i class="bi bi-upload me-2"></i>Upload Assignment</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (empty($availableAssignments)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                            <h4>No assignments to submit</h4>
                            <p class="text-muted">You have already submitted all available assignments or there are no assignments assigned to your class yet.</p>
                            <a href="assignments.php" class="btn btn-grad">
                                <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                            </a>
                        </div>
                    <?php else: ?>
                        <form action="upload_assignment.php" method="post" enctype="multipart/form-data" id="uploadForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Assignment</label>
                                <div id="assignmentList">
                                    <?php foreach ($availableAssignments as $assignment): ?>
                                        <div class="assignment-option" data-id="<?php echo $assignment['id']; ?>">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($assignment['subject']); ?></small>
                                                </div>
                                                <div class="text-end">
                                                    <small class="<?php echo (new DateTime() > new DateTime($assignment['due_date'])) ? 'due-date-warning' : 'due-date-normal'; ?>">
                                                        Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="assignment_id" id="selectedAssignmentId" required>
                                <div class="invalid-feedback">Please select an assignment.</div>
                            </div>

                            <div class="mb-3">
                                <label for="file" class="form-label fw-bold">Select File</label>
                                <input class="form-control" type="file" id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                <div class="form-text">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</div>
                            </div>

                            <div class="mb-4">
                                <label for="comments" class="form-label fw-bold">Comments (optional)</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Add any comments or notes about your submission..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-grad btn-lg" id="submitBtn" disabled>
                                    <i class="bi bi-upload me-1"></i>Submit Assignment
                                </button>
                                <a href="assignments.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                                </a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Assignment selection logic
        document.querySelectorAll('.assignment-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.assignment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });

                // Add selected class to clicked option
                this.classList.add('selected');

                // Set hidden input value
                document.getElementById('selectedAssignmentId').value = this.dataset.id;

                // Enable submit button
                document.getElementById('submitBtn').disabled = false;

                // Remove invalid feedback
                document.getElementById('selectedAssignmentId').classList.remove('is-invalid');
            });
        });

        // Form validation
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            const assignmentId = document.getElementById('selectedAssignmentId').value;
            if (!assignmentId) {
                e.preventDefault();
                document.getElementById('selectedAssignmentId').classList.add('is-invalid');
                return false;
            }
        });

        // File validation
        document.getElementById('file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                const maxSize = 10 * 1024 * 1024; // 10MB

                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only PDF, DOC, DOCX, JPG, PNG allowed.');
                    e.target.value = '';
                    return;
                }

                if (file.size > maxSize) {
                    alert('File size too large. Maximum 10MB allowed.');
                    e.target.value = '';
                    return;
                }
            }
        });

        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
