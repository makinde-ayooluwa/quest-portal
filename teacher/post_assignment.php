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

// Check for pre-selected class from query parameter
$selectedClass = '';
if (isset($_GET['class'])) {
    $requestedClass = $_GET['class'];
    foreach ($assignedClasses as $class) {
        if ($class['class_name'] === $requestedClass) {
            $selectedClass = $requestedClass;
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Post Assignment - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .form-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-top: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-container">
                        <h2 class="mb-4 text-center">Post New Assignment</h2>
                        <form action="post_assignment_handler.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="class_name" class="form-label">Class</label>
                                <select class="form-control" id="class_name" name="class_name" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($assignedClasses as $class) { ?>
                                        <option value="<?php echo htmlspecialchars($class['class_name']); ?>" <?php echo ($selectedClass === $class['class_name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($class['class_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Attachment (optional)</label>
                                <input type="file" class="form-control" id="file" name="file">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Post Assignment</button>
                                <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
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
