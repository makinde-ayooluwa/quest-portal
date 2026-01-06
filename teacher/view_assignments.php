<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}



$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$assignedClasses = $teacher->getAssignedClasses($pdo, $email);

// Check for class filter
$selectedClass = isset($_GET['class']) ? $_GET['class'] : 'all';

// Get assignments based on filter
if ($selectedClass === 'all') {
    // Get all assignments posted by this teacher
    $allAssignments = [];
    foreach ($assignedClasses as $class) {
        $assignments = $teacher->getAssignmentsForClass($pdo, $class["class_name"], $teacherData['id']);
        $allAssignments = array_merge($allAssignments, $assignments);
    }
} else {
    // Get assignments for specific class
    $allAssignments = $teacher->getAssignmentsForClass($pdo, $selectedClass, $teacherData['id']);
}

// Sort assignments by creation date (newest first)
usort($allAssignments, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Assignments - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .assignments-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
        }

        .assignment-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .assignment-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .assignment-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .assignment-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .assignment-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .submission-count {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .submission-count {
                font-size: 0.75rem;
                padding: 0.2rem 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .submission-count {
                font-size: 0.7rem;
                padding: 0.15rem 0.4rem;
            }
        }

        .btn-view-submissions {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-view-submissions:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white;
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
                <h2>My Assignments</h2>
                <a href="post_assignment.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Post New Assignment
                </a>
            </div>

            <!-- Class Filter -->
            <div class="assignments-container mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label for="classFilter" class="form-label fw-bold">Filter by Class:</label>
                        <select class="form-select" id="classFilter">
                            <option value="all" <?php echo ($selectedClass === 'all') ? 'selected' : ''; ?>>All Classes</option>
                            <?php foreach ($assignedClasses as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['class_name']); ?>" <?php echo ($selectedClass === $class['class_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <?php if ($selectedClass === 'all'): ?>
                                Showing assignments from all classes
                            <?php else: ?>
                                Showing assignments for <?php echo htmlspecialchars($selectedClass); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="assignments-container">
                <?php if (empty($allAssignments)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                        <h4>No assignments posted yet</h4>
                        <p class="text-muted">You haven't posted any assignments yet.</p>
                        <a href="post_assignment.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Post Your First Assignment
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($allAssignments as $assignment): ?>
                        <?php
                        $submissions = $teacher->getSubmittedAssignments($pdo, $assignment['id']);
                        $submissionCount = count($submissions);
                        $dueDate = new DateTime($assignment['due_date']);
                        $today = new DateTime();
                        $isOverdue = $today > $dueDate;
                        ?>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <div>
                                    <h5 class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                    <div class="assignment-meta">
                                        <span><strong>Subject:</strong> <?php echo htmlspecialchars($assignment['subject']); ?></span>
                                        <span><strong>Class:</strong> <?php echo htmlspecialchars($assignment['class_name']); ?></span>
                                        <span><strong>Due:</strong> <?php echo $dueDate->format('M j, Y'); ?> <?php if ($isOverdue): ?><span class="text-danger">(Overdue)</span><?php endif; ?></span>
                                        <span><strong>Posted:</strong> <?php echo date('M j, Y', strtotime($assignment['created_at'])); ?></span>
                                    </div>
                                    <?php if (!empty($assignment['description'])): ?>
                                        <p class="mt-2 text-muted"><?php echo nl2br(htmlspecialchars(substr($assignment['description'], 0, 150))); ?><?php if (strlen($assignment['description']) > 150): ?>...<?php endif; ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <span class="submission-count"><?php echo $submissionCount; ?> Submissions</span>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="btn btn-view-submissions btn-sm">
                                    <i class="bi bi-eye me-1"></i>View Submissions
                                </a>
                                <?php if (!empty($assignment['file_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-download me-1"></i>Download Assignment
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>



    <script>
        // Filter assignments by class
        function filterByClass(className) {
            if (className == 'all') {
                window.location.href = 'view_assignments.php';
            } else {
                window.location.href = 'view_assignments.php?class=' + encodeURIComponent(className);
            }
        }

        // Attach event listener to class filter dropdown
        const classFilter = document.getElementById('classFilter');
        if (classFilter) {
            classFilter.addEventListener('change', function() {
                filterByClass(this.value);
            });
        }

    </script>
</body>

</html>
