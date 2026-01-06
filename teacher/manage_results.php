<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$results = $teacher->getResultsForTeacher($pdo, $email);
$assignedClasses = $teacher->getAssignedClasses($pdo, $email);

// Get unique academic terms for filter
$academicTerms = array_unique(array_column($results, 'academic_term'));
sort($academicTerms);

// Get unique classes for filter
$classes = array_unique(array_column($results, 'class'));
sort($classes);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Results - QUEST TEACHER</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .results-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .filter-section {
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

        .upload-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .progress {
            height: 7px;
        }

        .btn-upload {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
            border: none;
            color: white;
        }

        .btn-upload:hover {
            background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
            color: white;
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

        .bg-grad {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .btn-grad {
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }

        .btn-grad:hover {
            background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
        }

        .bg-yellow {
            background: var(--quest-yellow);
        }

        .bg-green {
            background: var(--quest-green);
        }

        .stats-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .result-item {
            transition: all 0.2s ease;
        }

        .result-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .badge-term {
            background: var(--quest-green);
            color: white;
        }

        .badge-class {
            background: var(--quest-yellow);
            color: #000;
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
        <section class="results-management">
            <div class="container-fluid">
                <div class="results-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">Student Results Management</h2>
                            <p class="mb-0 text-muted">Manage and upload student academic results</p>
                        </div>
                        <button class="btn btn-grad" data-bs-toggle="modal" data-bs-target="#uploadResultModal">
                            <i class="bi bi-upload me-2"></i>Upload New Result
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats row mb-4">
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-file-earmark-text-fill px-3"></i>Total Results</h3>
                            <h1 class="fs-2"><?php echo count($results); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-success" style="width: <?php echo min((count($results) / 100) * 100, 100); ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-calendar-fill px-3"></i>Academic Terms</h3>
                            <h1 class="fs-2"><?php echo count($academicTerms); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-primary" style="width: <?php echo min((count($academicTerms) / 10) * 100, 100); ?>%;"></div>
                            </div>
                        </div>
                        <div class="stat-card col-md">
                            <h3><i class="bi bi-building-fill px-3"></i>Classes Covered</h3>
                            <h1 class="fs-2"><?php echo count($classes); ?></h1>
                            <div class="progress mt-3">
                                <div class="progress-bar bg-warning" style="width: <?php echo min((count($classes) / 20) * 100, 100); ?>%;"></div>
                            </div>
                        </div>
                    </div>



                    <!-- Filters -->
                    <div class="filter-section">
                        <h5 class="mb-3"><i class="bi bi-funnel me-2"></i>Filter Results</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by student name or admission number...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="termFilter">
                                    <option value="">All Academic Terms</option>
                                    <?php foreach ($academicTerms as $term): ?>
                                        <option value="<?php echo htmlspecialchars($term); ?>"><?php echo htmlspecialchars($term); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="classFilter">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class); ?>"><?php echo htmlspecialchars($class); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="bi bi-x-circle me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle" id="resultsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Admission No</th>
                                    <th>Class</th>
                                    <th>Academic Term</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($results)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-file-earmark-x fs-2 text-muted mb-2"></i>
                                            <div class="text-muted">No results uploaded yet.</div>
                                            <small class="text-muted">Click "Upload New Result" to add student results.</small>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($results as $result): ?>
                                        <tr class="result-item">
                                            <td><?php echo htmlspecialchars($result['fullname']); ?></td>
                                            <td><?php echo htmlspecialchars($result['admission_number']); ?></td>
                                            <td><span class="badge badge-class"><?php echo htmlspecialchars($result['class']); ?></span></td>
                                            <td><span class="badge badge-term"><?php echo htmlspecialchars($result['academic_term']); ?></span></td>
                                            <td><?php echo date('M j, Y', strtotime($result['added_on'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a download href="../assets/uploads/results/<?php echo htmlspecialchars($result['result_file']); ?>"
                                                       class="btn btn-sm btn-success" target="_blank" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteResult(<?php echo $result['id']; ?>, '<?php echo htmlspecialchars($result['fullname']); ?>')" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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

    <!-- Upload Result Modal -->
    <div class="modal fade" id="uploadResultModal" tabindex="-1" aria-labelledby="uploadResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadResultModalLabel"><i class="bi bi-upload me-2"></i>Upload New Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="upload_result_handler.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="academic_term" class="form-label">Academic Term</label>
                                <input type="text" class="form-control" id="academic_term" name="academic_term" placeholder="e.g., 2023/2024 First Term" required>
                            </div>
                            <div class="col-md-6">
                                <label for="student_admission_number" class="form-label">Student Admission Number</label>
                                <input type="text" class="form-control" id="student_admission_number" name="student_admission_number" placeholder="e.g., ADM001" required>
                            </div>
                            <div class="col-12">
                                <label for="result_file" class="form-label">Result File (PDF, DOC, DOCX)</label>
                                <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.doc,.docx" required>
                                <div class="form-text">Maximum file size: 10MB</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-grad">
                                <i class="bi bi-upload me-2"></i>Upload Result
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the result for <strong id="studentName"></strong>?
                    <br><small class="text-muted">This action cannot be undone and will also delete the uploaded file.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete Result</button>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete result functionality
        let resultToDelete = null;

        function deleteResult(resultId, studentName) {
            resultToDelete = resultId;
            document.getElementById('studentName').textContent = studentName;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (resultToDelete) {
                window.location.href = 'delete_result_handler.php?result_id=' + resultToDelete;
            }
        });

        // Filter functionality
        document.getElementById('searchInput').addEventListener('input', filterResults);
        document.getElementById('termFilter').addEventListener('change', filterResults);
        document.getElementById('classFilter').addEventListener('change', filterResults);

        function filterResults() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const termFilter = document.getElementById('termFilter').value.toLowerCase();
            const classFilter = document.getElementById('classFilter').value.toLowerCase();

            const rows = document.querySelectorAll('#resultsTable tbody tr');

            rows.forEach(row => {
                if (row.cells.length < 6) return; // Skip if not a data row

                const studentName = row.cells[0].textContent.toLowerCase();
                const admissionNo = row.cells[1].textContent.toLowerCase();
                const className = row.cells[2].textContent.toLowerCase();
                const term = row.cells[3].textContent.toLowerCase();

                const matchesSearch = studentName.includes(searchTerm) || admissionNo.includes(searchTerm);
                const matchesTerm = !termFilter || term.includes(termFilter);
                const matchesClass = !classFilter || className.includes(classFilter);

                row.style.display = (matchesSearch && matchesTerm && matchesClass) ? '' : 'none';
            });
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('termFilter').value = '';
            document.getElementById('classFilter').value = '';
            filterResults();
        }

        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
