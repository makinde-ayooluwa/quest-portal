<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$id = isset($_GET["id"]) ? $_GET["id"] : null;
function getId($pdo, $id)
{
    $query = "SELECT * FROM classes_names_only WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        return true;
    } else {
        return false;
    }
}

// Fetch class details
$classData = null;
$teachersData = [];
$students = [];
$assignments = [];
$classStats = [];
$_SESSION["currentClass"] = $id;

if ($id && getId($pdo, $id)) {
    // Get class info
    $query = "SELECT * FROM classes_names_only WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $classData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get all teachers assigned to this class
    if ($classData) {
        $mentorQuery = "SELECT c.*, s.* FROM classes c
                       LEFT JOIN staffs s ON c.mentor_email = s.email
                       WHERE c.class_name = :class_name
                       ORDER BY s.fullname;";
        $mentorStmt = $pdo->prepare($mentorQuery);
        $mentorStmt->bindParam(":class_name", $classData['class_name']);
        $mentorStmt->execute();
        $teachersData = $mentorStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get students in this class
    $studentQuery = "SELECT id, fullname, admission_number, email FROM students WHERE class = :class_name ORDER BY fullname;";
    $studentStmt = $pdo->prepare($studentQuery);
    $studentStmt->bindParam(":class_name", $classData['class_name']);
    $studentStmt->execute();
    $students = $studentStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get assignments for this class
    $assignmentQuery = "SELECT a.*, COUNT(asub.id) as submission_count
                       FROM assignments a
                       LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id
                       WHERE a.class_name = :class_name
                       GROUP BY a.id
                       ORDER BY a.due_date DESC;";
    $assignmentStmt = $pdo->prepare($assignmentQuery);
    $assignmentStmt->bindParam(":class_name", $classData['class_name']);
    $assignmentStmt->execute();
    $assignments = $assignmentStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get class statistics
    $totalStudentsQuery = "SELECT COUNT(*) as total_students FROM students WHERE class = :class_name;";
    $totalStudentsStmt = $pdo->prepare($totalStudentsQuery);
    $totalStudentsStmt->bindParam(":class_name", $classData['class_name']);
    $totalStudentsStmt->execute();
    $totalStudents = $totalStudentsStmt->fetch(PDO::FETCH_ASSOC)['total_students'];

    $totalAssignmentsQuery = "SELECT COUNT(*) as total_assignments FROM assignments WHERE class_name = :class_name;";
    $totalAssignmentsStmt = $pdo->prepare($totalAssignmentsQuery);
    $totalAssignmentsStmt->bindParam(":class_name", $classData['class_name']);
    $totalAssignmentsStmt->execute();
    $totalAssignments = $totalAssignmentsStmt->fetch(PDO::FETCH_ASSOC)['total_assignments'];

    $totalSubmissionsQuery = "SELECT COUNT(*) as total_submissions FROM assignment_submissions asub
                              JOIN assignments a ON a.id = asub.assignment_id
                              WHERE a.class_name = :class_name AND asub.status = 'submitted';";
    $totalSubmissionsStmt = $pdo->prepare($totalSubmissionsQuery);
    $totalSubmissionsStmt->bindParam(":class_name", $classData['class_name']);
    $totalSubmissionsStmt->execute();
    $totalSubmissions = count($totalSubmissionsStmt->fetchAll(PDO::FETCH_ASSOC));

    $avgGradeQuery = "SELECT AVG(CAST(asub.grade AS DECIMAL(5,2))) as avg_grade FROM assignment_submissions asub
                      JOIN assignments a ON a.id = asub.assignment_id
                      WHERE a.class_name = :class_name AND asub.grade REGEXP '^[0-9]+$';";
    $avgGradeStmt = $pdo->prepare($avgGradeQuery);
    $avgGradeStmt->bindParam(":class_name", $classData['class_name']);
    $avgGradeStmt->execute();
    $avgGrade = $avgGradeStmt->fetch(PDO::FETCH_ASSOC)['avg_grade'];

    $classStats = [
        'total_students' => $totalStudents,
        'total_assignments' => $totalAssignments,
        'total_submissions' => $totalSubmissions,
        'avg_grade' => $avgGrade
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Class Management - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .class-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .stats-card.primary {
            border-left-color: #0d6efd;
        }

        .stats-card.success {
            border-left-color: #198754;
        }

        .stats-card.info {
            border-left-color: #0dcaf0;
        }

        .stats-card.warning {
            border-left-color: #ffc107;
        }

        .teacher-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .student-list {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .assignment-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .btn-grad {
            background: linear-gradient(90deg, #0d6efd 60%, #198754 100%);
            color: #fff;
            border: none;
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, #198754 60%, #0d6efd 100%);
            color: #fff;
        }

        .btn-outline-grad {
            background: transparent;
            color: #0d6efd;
            border: 2px solid #0d6efd;
        }

        .btn-outline-grad:hover {
            background: #0d6efd;
            color: #fff;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-custom {
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <?php
    if (!isset($_GET["id"])) {
        ?>
        <style>
            .error-container {
                max-width: 500px;
                margin: 5rem auto;
                padding: 2rem;
                background: #fff3f3;
                border: 1px solid #ffcccc;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            }

            .error-container h2 {
                color: #d9534f;
                margin-bottom: 1rem;
            }

            .error-container p {
                color: #555;
                margin-bottom: 1.5rem;
            }

            .error-container a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #d9534f;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .error-container a:hover {
                background: #c9302c;
            }
        </style>
        <div class="error-container">
            <h2>Error</h2>
            <p>Sorry, the method used to request this page is not allowed.</p>
            <a href="./">Go to Homepage</a>
        </div>
        <?php
    } else if (!$classData) {
        ?>
            <style>
                .error-container {
                    max-width: 500px;
                    margin: 5rem auto;
                    padding: 2rem;
                    background: #fff3f3;
                    border: 1px solid #ffcccc;
                    border-radius: 8px;
                    text-align: center;
                    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
                }

                .error-container h2 {
                    color: #d9534f;
                    margin-bottom: 1rem;
                }

                .error-container p {
                    color: #555;
                    margin-bottom: 1.5rem;
                }

                .error-container a {
                    display: inline-block;
                    padding: 0.5rem 1rem;
                    background: #d9534f;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 4px;
                    transition: background 0.3s ease;
                }

                .error-container a:hover {
                    background: #c9302c;
                }
            </style>
            <div class="error-container">
                <h2>Error</h2>
                <p>Class not found.</p>
                <a href="./">Go to Homepage</a>
            </div>
        <?php
    } else {
        $_SESSION["class_id"] = $id;
        include "header_sidebar.php";
        ?>

            <!-- Class Header -->
            <div class="class-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="mb-0"><?php echo htmlspecialchars($classData['class_name']); ?> Class</h1>
                                    <p class="mb-0 opacity-75">Class Management Dashboard</p>
                                </div>
                                <div class="text-end">
                                    <a href="index.php#classes" class="btn btn-light me-2">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Classes
                                    </a>
                                    <!-- <a href="add_assignment.php?class_id=<?php //echo $id; 
                                        ?>" class="btn btn-grad">
                                    <i class="bi bi-plus-circle me-1"></i>Add Assignment
                                </a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-lg-9">

                        <!-- Class Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card primary">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0"><?php echo $classStats['total_students'] ?? 0; ?></h3>
                                            <small class="text-muted">Total Students</small>
                                        </div>
                                        <i class="bi bi-people fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card success">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0"><?php echo $classStats['total_assignments'] ?? 0; ?></h3>
                                            <small class="text-muted">Assignments</small>
                                        </div>
                                        <i class="bi bi-journal-check fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card info">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0"><?php echo $classStats['total_submissions'] ?? 0; ?></h3>
                                            <small class="text-muted">Submissions</small>
                                        </div>
                                        <i class="bi bi-upload fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-3">
                                <div class="stats-card warning">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h3 class="mb-0">
                                            <?php // echo $classStats['avg_grade'] ? number_format($classStats['avg_grade'], 1) : 'N/A'; ?>
                                            </h3>
                                            <small class="text-muted">Avg Grade</small>
                                        </div>
                                        <i class="bi bi-graph-up fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <!-- Teacher Assignment Section -->
                        <div class="teacher-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0"><i class="bi bi-person-badge me-2"></i>Class Teachers
                                    (<?php echo count($teachersData); ?>)</h3>
                                <button class="btn btn-outline-grad btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#assignTeacherModal">
                                    <i class="bi bi-pencil me-1"></i>Assign Teacher
                                </button>
                            </div>

                        <?php if (!empty($teachersData)): ?>
                            <?php foreach ($teachersData as $teacher): ?>
                                    <div class="row align-items-center mb-4 pb-4 border-bottom">
                                        <div class="col-md-2">
                                            <img src="<?php // echo htmlspecialchars($teacher['picture'] ?? 'assets/images/no-picture.jpg'); 
                                                        switch ($teacher["staff_role"]) {
                                                            case "Admin":
                                                                echo $teacher['picture'];
                                                                break;
                                                            case "Teacher":
                                                                echo "../teacher/" . $teacher['picture'];
                                                                break;
                                                            default:
                                                                echo "assets/images/no-picture.jpg";
                                                                break;
                                                        }
                                                        ?>" alt="Teacher" class="rounded-circle" width="80" height="80"
                                                style="object-fit: cover;">
                                        </div>
                                        <div class="col-md-8">
                                            <h4 class="mb-1"><?php echo htmlspecialchars($teacher['fullname']); ?></h4>
                                            <p class="text-muted mb-2"><?php echo htmlspecialchars($teacher['email']); ?></p>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <small class="text-muted">Phone</small>
                                                    <p class="mb-0"><?php echo htmlspecialchars($teacher['phone']); ?></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Subject</small>
                                                    <p class="mb-0"><?php echo htmlspecialchars($teacher['subject_given'] ?? 'N/A'); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Role</small>
                                                    <p class="mb-0"><?php echo htmlspecialchars($teacher['staff_role']); ?></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <small class="text-muted">Status</small>
                                                    <p class="mb-0">
                                                        <span
                                                            class="badge bg-<?php echo $teacher['staff_status'] === 'Active' ? 'success' : 'secondary'; ?>">
                                                        <?php echo htmlspecialchars($teacher['staff_status']); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="unassign_mentor.php?mentor_email=<?php echo urlencode($teacher['mentor_email']); ?>&class_name=<?php echo urlencode($classData['class_name']); ?>"
                                                class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-person-dash me-1"></i>Unassign
                                            </a>
                                        </div>
                                    </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-person-x fs-1 text-muted mb-3"></i>
                                    <h5>No teachers assigned</h5>
                                    <p class="text-muted">Assign teachers to manage this class.</p>
                                    <button class="btn btn-grad" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
                                        <i class="bi bi-plus-circle me-1"></i>Assign Teacher
                                    </button>
                                </div>
                        <?php endif; ?>
                        </div>

                        <!-- Students List -->
                        <div class="student-list">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0"><i class="bi bi-people me-2"></i>Students (<?php echo count($students); ?>)
                                </h3>
                                <a href="students.php?class_filter=<?php echo urlencode($classData['class_name']); ?>"
                                    class="btn btn-outline-grad btn-sm">
                                    <i class="bi bi-eye me-1"></i>View All
                                </a>
                            </div>

                        <?php if (empty($students)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-people fs-1 text-muted mb-3"></i>
                                    <h5>No students enrolled</h5>
                                    <p class="text-muted">Students will appear here once they are enrolled in this class.</p>
                                </div>
                        <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Admission No.</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['admission_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                    <td>
                                                        <a href="view_student.php?id=<?php echo $student['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php endif; ?>
                        </div>

                        <!-- Assignments Section -->
                        <div class="assignment-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="mb-0"><i class="bi bi-journal-check me-2"></i>Assignments
                                    (<?php echo count($assignments); ?>)</h3>
                                <!-- <a href="add_assignment.php?class_id=<?php echo $id; ?>" class="btn btn-grad btn-sm">
                                <i class="bi bi-plus-circle me-1"></i>Add Assignment
                            </a> -->
                            </div>

                        <?php if (empty($assignments)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                    <h5>No assignments yet</h5>
                                    <p class="text-muted">Create assignments for this class to track student progress.</p>
                                    <!-- <a href="add_assignment.php?class_id=<?php echo $id; ?>" class="btn btn-grad">
                                    <i class="bi bi-plus-circle me-1"></i>Create First Assignment
                                </a> -->
                                </div>
                        <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Subject</th>
                                                <th>Due Date</th>
                                                <th>Submissions</th>
                                                <!-- <th>Actions</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($assignments as $assignment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($assignment['subject']); ?></td>
                                                    <td>
                                                        <?php
                                                        $dueDate = new DateTime($assignment['due_date']);
                                                        $today = new DateTime();
                                                        $isOverdue = $today > $dueDate;
                                                        ?>
                                                        <span class="<?php echo $isOverdue ? 'text-danger fw-bold' : ''; ?>">
                                                        <?php echo $dueDate->format('M j, Y'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-custom bg-info"><?php echo $assignment['submission_count']; ?>
                                                            submitted</span>
                                                    </td>
                                                    <!-- <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="view_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="edit_assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    </div>
                                                </td> -->
                                                </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                        <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Assign Teacher Modal -->
            <div class="modal fade" id="assignTeacherModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Assign Teacher to <?php echo htmlspecialchars($classData['class_name']); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="edit_class_handler.php" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="class_name"
                                    value="<?php echo htmlspecialchars($classData['class_name']); ?>">
                                <div class="mb-3">
                                    <label class="form-label">Select Teacher</label>
                                    <select name="mentor_email" class="form-select" required>
                                        <option value="">Choose a teacher...</option>
                                        <?php
                                        $query = "SELECT * FROM staffs WHERE staff_role = 'Teacher' ORDER BY fullname;";
                                        $stmt = $pdo->prepare($query);
                                        $stmt->execute();
                                        $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($mentors as $mentor) {
                                            $selected = '';
                                            echo '<option value="' . htmlspecialchars($mentor['email']) . '" ' . $selected . '>' . htmlspecialchars($mentor['fullname']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-grad">Assign Teacher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php
    }
    ?>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <?php if (isset($_SESSION["error"])): ?>
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
        <?php unset($_SESSION["error"]); ?>
    <?php elseif (isset($_SESSION["success"])): ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!")
        </script>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>
</body>

</html>