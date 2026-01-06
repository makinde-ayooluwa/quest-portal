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

// Get selected class from URL parameter, default to first assigned class
$selectedClass = isset($_GET['class']) ? $_GET['class'] : ($assignedClasses ? $assignedClasses[0]['class_name'] : '');

// Verify selected class is assigned to teacher
$validClass = false;
foreach ($assignedClasses as $class) {
    if ($class['class_name'] === $selectedClass) {
        $validClass = true;
        break;
    }
}
if (!$validClass && $assignedClasses) {
    $selectedClass = $assignedClasses[0]['class_name'];
}

// Get students for selected class
$students = $selectedClass ? $teacher->getStudentsInClass($pdo, $selectedClass) : [];

// Helper to safely echo
function e($v) { return htmlspecialchars($v ?? ''); }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Students - QUEST TEACHER</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .students-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .class-selector {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .table-responsive {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-top: 1rem;
        }

        .student-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #f1f5ff;
        }

        .stats-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .student-item {
            transition: all 0.2s ease;
        }

        .student-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        :root {
            --quest-yellow: #fec511;
            --quest-green: #5aac7b;
        }

        .text-green {
            color: var(--quest-green);
        }

        .text-yellow {
            color: var(--quest-yellow);
        }

        .btn-grad {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
            border: none;
            color: white;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
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
        <section class="students-management">
            <div class="container-fluid">
                <div class="students-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">Student Management</h2>
                            <p class="mb-0 text-muted">View and manage students in your assigned classes</p>
                        </div>
                    </div>

                    <!-- Class Selector -->
                    <div class="class-selector">
                        <h5 class="mb-3"><i class="bi bi-journal-bookmark me-2"></i>Select Class</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <select class="form-select" id="classSelector" onchange="changeClass()">
                                    <?php foreach ($assignedClasses as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class['class_name']); ?>" <?php echo $class['class_name'] === $selectedClass ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['class_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats row mb-4">
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-people-fill px-3"></i>Total Students</h3>
                            <h1 class="fs-2"><?php echo count($students); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-success" style="width: <?php echo min((count($students) / 50) * 100, 100); ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-gender-male px-3"></i>Boys</h3>
                            <h1 class="fs-2"><?php echo count(array_filter($students, function($s) { return strtolower($s['gender'] ?? '') === 'male'; })); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-primary" style="width: <?php echo count($students) > 0 ? (count(array_filter($students, function($s) { return strtolower($s['gender'] ?? '') === 'male'; })) / count($students)) * 100 : 0; ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-gender-female px-3"></i>Girls</h3>
                            <h1 class="fs-2"><?php echo count(array_filter($students, function($s) { return strtolower($s['gender'] ?? '') === 'female'; })); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-warning" style="width: <?php echo count($students) > 0 ? (count(array_filter($students, function($s) { return strtolower($s['gender'] ?? '') === 'female'; })) / count($students)) * 100 : 0; ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle" id="studentsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Student Name</th>
                                    <th>Admission No</th>
                                    <th>Gender</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($students)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-people-x fs-2 text-muted mb-2"></i>
                                            <div class="text-muted">No students found in this class.</div>
                                            <small class="text-muted">Select a different class to view students.</small>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr class="student-item">
                                            <td>
                                                <img src="<?php echo e('../' . $student['picture']); ?>" alt="<?php echo e($student['fullname']); ?>" class="student-photo">
                                            </td>
                                            <td><?php echo e($student['fullname']); ?></td>
                                            <td><?php echo e($student['admission_number']); ?></td>
                                            <td><?php echo e($student['gender']); ?></td>
                                            <td><?php echo e($student['email']); ?></td>
                                            <td><?php echo e($student['phone']); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="view_student.php?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success" title="View Profile">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="mailto:<?php echo e($student['email']); ?>" class="btn btn-sm btn-primary" title="Send Email">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeClass() {
            const selectedClass = document.getElementById('classSelector').value;
            window.location.href = 'view_students.php?class=' + encodeURIComponent(selectedClass);
        }

        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
