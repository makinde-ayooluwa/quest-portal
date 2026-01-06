<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['student_id'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$studentId = (int)$_GET['student_id'];

// Verify the student is in one of the teacher's assigned classes
$query = "SELECT s.* FROM students s
          INNER JOIN classes c ON s.class = c.class_name
          WHERE s.id = :student_id AND c.mentor_email = :teacher_email
          LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':student_id', $studentId);
$stmt->bindParam(':teacher_email', $email);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: index.php");
    exit();
}

// Helper to safely echo
function e($v) { return htmlspecialchars($v ?? ''); }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Student - <?php echo e($student['fullname']); ?> - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .profile-card {
            max-width: 900px;
            margin: 2.5rem auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            padding: 1.25rem;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #f1f5ff;
        }

        .kv {
            font-weight: 600;
            color: #333;
        }

        .kvv {
            color: #555;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            text-align: center;
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
                    <h2>Student Profile</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Student Profile</li>
                        </ol>
                    </nav>
                </div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>

            <div class="profile-card">
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <img src="<?php echo e('../' . $student['picture']); ?>" alt="<?php echo e($student['fullname']); ?>" class="profile-photo mb-3">
                        <h5 class="mb-0"><?php echo e($student['fullname']); ?></h5>
                        <div class="text-muted small">Admission #: <?php echo e($student['admission_number']); ?></div>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <div class="kv">Class</div>
                                <div class="kvv"><?php echo e($student['class']); ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="kv">Email</div>
                                <div class="kvv"><?php echo e($student['email']); ?></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <div class="kv">Phone</div>
                                <div class="kvv"><?php echo e($student['phone']); ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="kv">Date of Birth</div>
                                <div class="kvv"><?php echo e($student['dob']); ?></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12">
                                <div class="kv">Home Address</div>
                                <div class="kvv"><?php echo e($student['home_address']); ?></div>
                            </div>
                        </div>

                        <hr>
                        <h6>Parents / Guardians</h6>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <div class="kv">Father</div>
                                <div class="kvv"><?php echo e($student['father_name']); ?> &mdash; <?php echo e($student['father_phone']); ?> (<?php echo e($student['father_email']); ?>)</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="kv">Mother</div>
                                <div class="kvv"><?php echo e($student['mother_name']); ?> &mdash; <?php echo e($student['mother_phone']); ?> (<?php echo e($student['mother_email']); ?>)</div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="kv">Admission Date</div>
                                <div class="kvv"><?php echo e($student['admission_date']); ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="kv">Account Verification</div>
                                <div class="kvv"><?php echo e($student['account_verification']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Statistics -->
            <div class="row mb-4">
                <?php
                // Get student's assignment submissions
                $studentAssignments = $teacher->getStudentAssignments($pdo, $studentId, $teacherData['id']);
                $totalAssignments = count($studentAssignments);
                $submittedAssignments = count(array_filter($studentAssignments, function($a) { return !empty($a['submission_id']); }));
                $gradedAssignments = count(array_filter($studentAssignments, function($a) { return !empty($a['grade']); }));
                ?>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $totalAssignments; ?></div>
                        <div class="text-muted">Total Assignments</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $submittedAssignments; ?></div>
                        <div class="text-muted">Submitted</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number"><?php echo $gradedAssignments; ?></div>
                        <div class="text-muted">Graded</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number">
                            <?php echo $totalAssignments > 0 ? round(($submittedAssignments / $totalAssignments) * 100, 1) : 0; ?>%
                        </div>
                        <div class="text-muted">Submission Rate</div>
                    </div>
                </div>
            </div>

            <!-- Recent Assignments -->
            <div class="profile-card">
                <h5 class="mb-3">Recent Assignments</h5>
                <?php if (empty($studentAssignments)): ?>
                    <p class="text-muted">No assignments found for this student.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($studentAssignments, 0, 10) as $assignment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['subject']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                                        <td>
                                            <?php if (!empty($assignment['submission_id'])): ?>
                                                <span class="badge bg-success">Submitted</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Not Submitted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo !empty($assignment['grade']) ? htmlspecialchars($assignment['grade']) : '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
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
    </script>
</body>

</html>
