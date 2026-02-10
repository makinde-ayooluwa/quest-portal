<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

$admin = new Admin($_SESSION["admin"]);
$adminData = $admin->adminData($pdo, $_SESSION["admin"]);
$results = $admin->getAllResults($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Results | Quest Portal</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .main-content {
            margin-left: 220px;
            padding: 2rem;
        }

        .results-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: #343a40;
            color: #fff;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .student-details h6 {
            margin: 0;
            font-weight: 600;
            color: #212529;
        }

        .student-details small {
            color: #6c757d;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #0d6efd;
            color: #fff;
        }

        .btn-edit:hover {
            background: #0b5ed7;
            color: #fff;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
        }

        .btn-delete:hover {
            background: #bb2d3b;
            color: #fff;
        }

        .result-link {
            color: #0d6efd;
            text-decoration: none;
        }

        .result-link:hover {
            text-decoration: underline;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 12px 12px 0 0;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .student-info {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-action {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>

    <?php include "header_sidebar.php" ?>

    <?php if (isset($_SESSION['error'])) { ?>
        <script>
            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
        </script>
    <?php
        unset($_SESSION['error']);
    } else if (isset($_SESSION['success'])) { ?>
        <script>
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
        </script>
    <?php
        unset($_SESSION['success']);
    } ?>

    <div class="main-content">
        <style>
            body[data-theme='dark']>* {
                color: #fff;
            }

            body[data-theme='dark'] .results-card {
                background: #000;
                box-shadow: 0 4px 15px rgb(255, 255, 255, 0.2);
            }
        </style>
        <div class="results-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-clipboard-data me-2"></i>Manage Results</h2>
                    <p class="mb-0">View, edit, and manage all uploaded student results</p>
                </div>
                <a href="upload_result.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Upload New Result
                </a>
            </div>

            <?php if (empty($results)) { ?>
                <div class="no-results">
                    <i class="bi bi-clipboard-x display-1 mb-3"></i>
                    <h4>No Results Found</h4>
                    <p>Start by uploading student results using the button above.</p>
                    <a href="upload_result.php" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Upload First Result
                    </a>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Academic Term</th>
                                <th>Result File</th>
                                <th>Uploaded Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result) { ?>
                                <tr>
                                    <td>
                                        <div class="student-info">
                                            <img src="<?php
                                                        function getStudentImage($pdo, $admission)
                                                        {
                                                            $query = "SELECT picture FROM students WHERE admission_number = :admission";
                                                            $stmt = $pdo->prepare($query);
                                                            $stmt->bindParam(":admission", $admission);
                                                            if ($stmt->execute()) {
                                                                return "../" . $stmt->fetch(PDO::FETCH_ASSOC)["picture"];
                                                            }
                                                        }
                                                        echo getStudentImage($pdo, $result["student_admission_number"]) ?>"
                                                alt="Student" class="student-avatar">
                                            <div class="student-details">
                                                <h6><?php echo htmlspecialchars($result['student_name'] ?? 'Unknown Student'); ?></h6>
                                                <small><?php echo htmlspecialchars($result['student_admission_number']); ?> | <?php echo htmlspecialchars($result['student_class'] ?? 'N/A'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($result['academic_term']); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($result['result_file']); ?>"
                                            target="_blank" class="result-link">
                                            <i class="bi bi-link-45deg me-1"></i>View Result
                                        </a>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($result['added_on'])); ?></td>
                                    <td>
                                        <button class="btn-action btn-edit me-2"
                                            onclick="editResult(<?php echo $result['id']; ?>, '<?php echo addslashes($result['academic_term']); ?>', '<?php echo addslashes($result['result_file']); ?>')">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                        <button class="btn-action btn-delete"
                                            onclick="deleteResult(<?php echo $result['id']; ?>, '<?php echo addslashes($result['student_name'] ?? 'Unknown Student'); ?>')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Edit Result Modal -->
    <div class="modal fade" id="editResultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editResultForm" action="update_result_handler.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" id="editResultId" name="result_id">
                        <div class="mb-3">
                            <label class="form-label">Academic Term <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editAcademicTerm" name="academic_term" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Result URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="editResultFile" name="result_file" required>
                            <div class="form-text">Provide a link to the student's result document.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Result</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteResultModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the result for <strong id="deleteStudentName"></strong>?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteResultForm" action="delete_result_handler.php" method="post" style="display: inline;">
                        <input type="hidden" id="deleteResultId" name="result_id">
                        <button type="submit" class="btn btn-danger">Delete Result</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <script>
        function editResult(id, academicTerm, resultFile) {
            document.getElementById('editResultId').value = id;
            document.getElementById('editAcademicTerm').value = academicTerm;
            document.getElementById('editResultFile').value = resultFile;
            new bootstrap.Modal(document.getElementById('editResultModal')).show();
        }

        function deleteResult(id, studentName) {
            document.getElementById('deleteResultId').value = id;
            document.getElementById('deleteStudentName').textContent = studentName;
            new bootstrap.Modal(document.getElementById('deleteResultModal')).show();
        }

        // Form validation
        document.getElementById('editResultForm').addEventListener('submit', function(e) {
            const academicTerm = this.querySelector('input[name="academic_term"]').value.trim();
            const resultUrl = this.querySelector('input[name="result_file"]').value.trim();

            if (!academicTerm) {
                e.preventDefault();
                toastr.error('Please enter the academic term.', 'Validation Error');
                return false;
            }

            if (!resultUrl) {
                e.preventDefault();
                toastr.error('Please provide a result URL.', 'Validation Error');
                return false;
            }

            try {
                new URL(resultUrl);
            } catch {
                e.preventDefault();
                toastr.error('Please enter a valid URL.', 'Validation Error');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>