<?php

session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

$id = isset($_GET["id"]) ? $_GET["id"] : "";

function idDoesNotMatch($pdo, $id)
{
    $query = "SELECT * FROM classes_names_only WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return false;
    } else {
        return true;
    }
}
if (!isset($_GET["id"])) {
?>
    Page Unavailable
<?php
} else if (idDoesNotMatch($pdo, $id)) {
?>
    Page Unavailable
<?php
} else {
    $class = $admin->viewClass($pdo, $id);
    $teacherDetails = $admin->getClassTeacherDetails($pdo, $id);
    $className = $admin->getClassName($pdo, $id);
    $_SESSION["currentClass"] = $id;
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Class View - QUEST PORTAL</title>
        <?php include "head.php" ?>
        <style>
            .class {
                margin-top: 0%;
            }

            .class .class-mentor-details {
                border-radius: 10px;
                padding: 0px;
                display: grid;
            }

            .students-page {
                border-left: 0.1px solid rgb(0, 0, 0, 0.13);
                overflow-y: scroll;
                height: 100vh;
            }

            .student-card {
                box-shadow: none;
                transform: translateY(0px);
                transition: all 0.3s ease;
            }

            .student-card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.64);
                transform: translateY(-5px);
            }

            @media(max-width:992px) {
                .class .class-mentor-details {
                    display: flex;
                }

                .students-page {
                    border-top: 0.1px solid rgb(0, 0, 0, 0.13);
                    border-left: none;
                }
            }
        </style>
    </head>

    <body>
        <?php include "header_sidebar.php" ?>
        <?php
        if (isset($_SESSION["error"])) {
        ?>
            <script>
                toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
            </script>
        <?php
            unset($_SESSION["error"]);
        } elseif (isset($_SESSION["success"])) {
        ?>
            <script>
                toastr.success("<?php echo $_SESSION["success"] ?>", "Success!")
            </script>
        <?php
            unset($_SESSION["success"]);
        }
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-9">
                    <h1 class="text-center text-uppercase">
                        <b><?php echo $className ?></b>
                    </h1>
                    <div class="class">
                        <div class="card rounded-3 p-3">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <div class="p-3 bg-light rounded-3 class-mentor-details">
                                        <h5 class="mb-3">Teacher(s)</h5>
                                        <?php if (empty($teacherDetails)) : ?>
                                            <div class="text-center py-4">
                                                <svg width="96" height="96" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3 21c0-3.866 3.582-7 9-7s9 3.134 9 7" stroke="#6c757d" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <p class="mt-2 mb-3 text-muted">No teacher assigned to this class.</p>
                                                <a href="edit_class.php?id=<?php echo urlencode($id); ?>" class="btn btn-sm btn-primary">Assign Teacher</a>
                                            </div>
                                        <?php else: ?>
                                            <div class="list-unstyled">
                                                <?php foreach ($teacherDetails as $teacher) :
                                                    // fetch staff record (picture, fullname, role)
                                                    $sStmt = $pdo->prepare('SELECT fullname,picture,staff_role FROM staffs WHERE email = :email LIMIT 1');
                                                    $sStmt->bindValue(':email', $teacher['mentor_email']);
                                                    $sStmt->execute();
                                                    $staff = $sStmt->fetch(PDO::FETCH_ASSOC);
                                                    $pic = $staff['picture'] ?? '';
                                                    $fullname = $staff['fullname'] ?? $teacher['mentor_name'];
                                                    $role = $staff['staff_role'] ?? '';
                                                ?>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="<?php //echo htmlspecialchars($pic ?: 'assets/images/no-picture.jpg', ENT_QUOTES);
                                                        switch($staff["staff_role"]){
                                                            case "Admin":
                                                                echo $pic;
                                                                break;
                                                                case "Teacher":
                                                                    echo "../teacher/" . $pic;
                                                                    break;
                                                                    default:
                                                                    echo "assets/images/no-picture.jpg";
                                                                    break;
                                                        }
                                                         ?>" width="64" height="64" class="rounded-circle me-3" alt="<?php echo htmlspecialchars($fullname, ENT_QUOTES); ?>">
                                                        <div>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($fullname, ENT_QUOTES); ?></div>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($role ?: 'Mentor', ENT_QUOTES); ?></div>
                                                            <div class="mt-2">
                                                                <a href="unassign_mentor.php?mentor_email=<?php echo urlencode($teacher['mentor_email']); ?>&class_name=<?php echo urlencode($className); ?>" class="btn btn-sm btn-outline-danger">Unassign</a>
                                                                <a href="view_staff.php?id=<?php echo urlencode($teacher['mentor_id_as_staff']); ?>" class="btn btn-sm btn-outline-secondary ms-2">View</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="students-page p-3 bg-white rounded-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h5 class="mb-0">Students</h5>
                                            <div>
                                                <a href="add_student.php" class="btn btn-sm btn-success">Add Student</a>
                                                <a href="students.php" class="btn btn-sm btn-outline-secondary ms-2">View All Students</a>
                                            </div>
                                        </div>

                                        <?php if (empty($class)) : ?>
                                            <div class="text-center py-5 text-muted">
                                                <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" stroke="#adb5bd" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3 21c0-3.866 3.582-7 9-7s9 3.134 9 7" stroke="#adb5bd" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <h6 class="mt-3">No students in <?php echo htmlspecialchars($className, ENT_QUOTES); ?></h6>
                                                <p class="small text-muted">You can add a student to this class</p>
                                                <div class="mt-3">
                                                    <a href="add_student.php" class="btn btn-primary btn-sm">Add Student</a>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="row g-3">
                                                <?php foreach ($class as $student) : ?>
                                                    <div class="col-6 col-md-4 col-lg-3">
                                                        <a href="view_student.php?id=<?php echo urlencode($student['id']); ?>" class="text-decoration-none text-reset">
                                                            <div class="card student-card h-100 text-center p-2">
                                                                <img src="<?php echo "../" . htmlspecialchars($student['picture'] ?? '../assets/images/no-picture.jpg', ENT_QUOTES); ?>" class="rounded mb-2" height="96" alt="<?php echo htmlspecialchars($student['fullname'] ?? '', ENT_QUOTES); ?>">
                                                                <div class="fw-semibold small"><?php echo htmlspecialchars($student['fullname'] ?? '', ENT_QUOTES); ?></div>
                                                                <div class="small text-muted"><?php echo htmlspecialchars($student['admission_number'] ?? '', ENT_QUOTES); ?></div>
                                                                <div class="mt-2 small">
                                                                    <?php if (($student['account_verification'] ?? '') === 'Verified') : ?>
                                                                        <span class="badge bg-success">Verified</span>
                                                                    <?php else : ?>
                                                                        <span class="badge bg-danger">Not verified</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
        <script>
            // Prevent right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        </script>
    </body>

    </html>
<?php
}
