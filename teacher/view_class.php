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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Classes - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .class-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }

        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .class-header {
            border-bottom: 2px solid var(--quest-green);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .student-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .student-item {
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .btn-outline-primary {
            border-color: var(--quest-green);
            color: var(--quest-green);
        }

        .btn-outline-primary:hover {
            background: var(--quest-green);
            border-color: var(--quest-green);
        }

        .stats-badge {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
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
            <h2 class="mb-4">My Assigned Classes</h2>

            <?php if (empty($assignedClasses)) { ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Classes Assigned</h4>
                    <p class="text-muted">You haven't been assigned to any classes yet.</p>
                </div>
            <?php } else { ?>
                <div class="row">
                    <?php foreach ($assignedClasses as $class) {
                        $students = $teacher->getStudentsInClass($pdo, $class['class_name']);
                        $studentCount = count($students);
                    ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="class-card">
                                <div class="class-header">
                                    <h4 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h4>
                                    <span class="stats-badge"><?php echo $studentCount; ?> Students</span>
                                </div>

                                <div class="student-list">
                                    <?php if (empty($students)) { ?>
                                        <p class="text-muted text-center py-3">No students enrolled</p>
                                    <?php } else { ?>
                                        <?php foreach ($students as $student) { ?>
                                            <div class="student-item">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($student['fullname']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">Admission #: <?php echo htmlspecialchars($student['admission_number']); ?></small>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary" onclick="window.location.href = 'view_student.php?student_id=<?php echo $student['id']; ?>'">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" onclick="window.location.href='post_assignment.php?class=<?php echo urlencode($class['class_name']); ?>'">
                                        <i class="bi bi-plus-circle"></i> Post Assignment
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="window.location.href='view_assignments.php?class=<?php echo urlencode($class['class_name']); ?>'">
                                        <i class="bi bi-list"></i> View Assignments
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </main>

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

        function viewStudent(studentId) {
            // Implement view student details
            alert('Viewing student details for ID: ' + studentId);
        }

        function postAssignment(className) {
            // Redirect to post assignment page with class pre-selected
            window.location.href = 'post_assignment.php?class=' + encodeURIComponent(className);
        }

        function viewAssignments(className) {
            // Implement view assignments for class
            alert('Viewing assignments for ' + className);
        }
    </script>
</body>

</html>
