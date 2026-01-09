<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
// Pagination setup (server-side)
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

try {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
    $totalStudents = (int)$countStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM students ORDER BY added_on ASC LIMIT :offset, :perpage");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $studentsPaged = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Students pagination error: ' . $e->getMessage());
    $studentsPaged = [];
    $totalStudents = 0;
}

// Fetch classes for promotion dropdown
try {
    $classStmt = $pdo->query("SELECT class_name FROM classes_names_only ORDER BY class_name ASC");
    $classes = $classStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log('Fetch classes error: ' . $e->getMessage());
    $classes = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Students - Quest Schools Admin</title>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .students-card {
            max-width: 1200px;
            margin: 2.5rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(24, 24, 24, 0.06);
            padding: 1.25rem;
        }

        .students-toolbar {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem
        }

        .students-search {
            max-width: 520px;
            width: 100%;
        }

        .student-photo {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e9eefb
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            vertical-align: middle
        }

        @media (max-width: 767px) {
            .students-card {
                padding: 1rem
            }

            .students-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem
            }
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php" ?>
    <?php
    if (isset($_SESSION["success"])) {
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
                <div class="students-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="mb-0"><i class="bi bi-people me-2"></i>Student Management</h3>
                        <div>
                            <!-- <a href="add_student.php" class="btn btn-primary btn-sm me-2"><i class="bi bi-person-plus"></i> Add Student</a> -->
                            <!-- <a href="upload_material.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-cloud-upload"></i> Upload Material</a> -->
                        </div>
                    </div>

                    <div class="students-toolbar">
                        <form class="d-flex students-search" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search students by name, email or admission number" aria-label="Search" id="studentSearch">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                        <div class="text-end text-muted small">Showing <?php echo is_array($studentsPaged) ? count($studentsPaged) : 0; ?> students</div>
                    </div>

                    <form id="bulkActionsForm" method="post" action="bulk_student_actions.php">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <select name="action" id="bulkActionSelect" class="form-select form-select-sm" style="width:220px">
                                <option value="">Bulk actions</option>
                                <option value="delete">Delete selected</option>
                                <option value="promote">Promote / Demote selected</option>
                            </select>
                            <input type="hidden" name="promote_to" id="promoteToInput" value="">
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                            <div class="ms-auto text-muted small">Select rows and choose an action</div>
                        </div>

                        <div class="table-responsive" style="overflow-x:scroll;">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px"><!--<input type="checkbox" id="selectAll">--></th>
                                        <th style="width:64px">Photo</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Admission #</th>
                                        <th>Verification</th>
                                        <th style="width:120px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTable">
                                    <?php if (empty($studentsPaged)) { ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">No students found.</td>
                                        </tr>
                                        <?php } else {
                                        foreach ($studentsPaged as $student) {
                                        ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected_ids[]" value="<?php echo $student['id']; ?>" class="rowCheckbox"></td>
                                                <td><img src="<?php echo htmlspecialchars('../' . $student['picture']); ?>" alt="<?php echo htmlspecialchars($student['fullname']); ?>" class="student-photo"></td>
                                                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($student['admission_number']); ?></td>
                                                <td><?php if ($student['account_verification'] == 'Not verified') {
                                                        echo '<span class="badge bg-danger">Not verified</span>';
                                                    } else {
                                                        echo '<span class="badge bg-success">Verified</span>';
                                                    } ?></td>
                                                <td>
                                                    <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-primary p-1 me-1" aria-label="View profile"><i class="bi bi-eye"></i></a>
                                                    <button type="button" class="btn btn-sm btn-outline-success me-1 p-1 promote-btn" data-id="<?php echo $student['id']; ?>" aria-label="Promote / Demote student"><i class="bi bi-arrow-up-circle"></i></button>
                                                    <button class="btn btn-sm p-1 btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#modal_<?php echo $student['id'] ?>" aria-label="Delete student"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            <!-- Modal for delete confirmation -->
                                            <div class="modal fade" id="modal_<?php echo $student['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Deletion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger fs-6">Are you sure you want to delete <strong><?php echo strtoupper(htmlspecialchars($student['fullname'])); ?></strong>?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <a href="delete_student.php?id=<?php echo $student['id'] ?>" class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <?php
                        $totalPages = (int)ceil($totalStudents / $perPage);
                        if ($totalPages > 1) {
                        ?>
                            <nav class="mt-3">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                    </li>
                                    <?php for ($p = 1; $p <= $totalPages; $p++) { ?>
                                        <li class="page-item <?php echo $p == $page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
                                    <?php } ?>
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php } ?>
                </div>
                <!-- Promote Modal -->
                <div class="modal fade" id="promoteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Promote / Demote selected students</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-2">Select destination class:</div>
                                <select id="promoteClassSelect" class="form-select">
                                    <?php foreach ($classes as $c) {
                                        echo '<option value="' . htmlspecialchars($c) . '">' . htmlspecialchars($c) . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmPromoteBtn" class="btn btn-primary btn-sm">Promote / Demote</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    // Simple search/filter for students table
                    document.querySelector('.students-card form').addEventListener('input', function(e) {
                        if (document.querySelector('.students-card form input').value == "") {
                            $("#selectAll").removeClass("d-none");
                        } else {
                            $("#selectAll").addClass("d-none");
                        }
                        e.preventDefault();
                        const query = document.getElementById('studentSearch').value.toLowerCase();
                        const rows = document.querySelectorAll('#studentsTable tr');
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(query) ? '' : 'none';
                        });
                    });
                    document.querySelector('.students-card form').addEventListener("submit", function(e) {
                        e.preventDefault();
                    });

                    // Select-all checkbox
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectAll = document.getElementById('selectAll');
                        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
                        if (selectAll) {
                            selectAll.addEventListener('change', function() {
                                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                            });
                        }

                        // Bulk actions form validation
                        const bulkForm = document.getElementById('bulkActionsForm');
                        if (bulkForm) {
                            bulkForm.addEventListener('submit', function(e) {
                                const action = document.getElementById('bulkActionSelect').value;
                                const checked = Array.from(document.querySelectorAll('.rowCheckbox')).filter(c => c.checked);
                                if (!action) {
                                    e.preventDefault();
                                    toastr.info("Please select a bulk action.");
                                    return;
                                }
                                if (checked.length === 0) {
                                    e.preventDefault();
                                    toastr.info("Please select at least one student.");
                                    return;
                                }
                                if (action === 'delete') {
                                    if (!confirm('Delete selected students? This cannot be undone.')) {
                                        e.preventDefault();
                                        return;
                                    }
                                }

                                // If Promote selected, open the promote modal instead of submitting
                                if (action === 'promote') {
                                    e.preventDefault();
                                    // show modal
                                    const promoteModalEl = document.getElementById('promoteModal');
                                    if (window.bootstrap && promoteModalEl) {
                                        const modal = new bootstrap.Modal(promoteModalEl);
                                        modal.show();
                                    } else {
                                        alert('Promote / Demote modal unavailable.');
                                    }
                                }
                            });
                        }

                        // Per-row promote buttons
                        document.querySelectorAll('.promote-btn').forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                if (!id) return;
                                // uncheck all checkboxes
                                document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = false);
                                // check this row's checkbox if present
                                const target = document.querySelector('.rowCheckbox[value="' + id + '"]');
                                if (target) target.checked = true;
                                // set action to promote and open modal
                                document.getElementById('bulkActionSelect').value = 'promote';
                                const promoteModalEl = document.getElementById('promoteModal');
                                if (window.bootstrap && promoteModalEl) {
                                    const modal = new bootstrap.Modal(promoteModalEl);
                                    modal.show();
                                }
                            });
                        });
                    });

                    // Wire up confirm button in promote modal
                    document.addEventListener('DOMContentLoaded', function() {
                        const confirmBtn = document.getElementById('confirmPromoteBtn');
                        if (confirmBtn) {
                            confirmBtn.addEventListener('click', function() {
                                const selectedClass = document.getElementById('promoteClassSelect').value;
                                if (!selectedClass) {
                                    alert('Please pick a destination class.');
                                    return;
                                }
                                // set hidden input and submit form
                                document.getElementById('promoteToInput').value = selectedClass;
                                // hide modal first
                                const modalEl = document.getElementById('promoteModal');
                                if (window.bootstrap && modalEl) {
                                    bootstrap.Modal.getInstance(modalEl)?.hide();
                                }
                                document.getElementById('bulkActionsForm').submit();
                            });
                        }
                    });
                </script>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
    <script>
        // Excel dynamic addition with the following params : [fullname,email,class,admission_number]
        function addStudents() {
            fetch("https://opensheet.elk.sh/17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o/Sheet1")
                .then(res => res.json())
                .then(data => {
                    console.log(data)
                    fetch("add_student_in_bulk.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(data)
                        })
                        .then(res => res.json())
                    // .then(result => console.log(result))
                    // .catch(err => console.error(err));
                    fetch("update_student_in_bulk.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    })
                    fetch("add_class_names.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                "data": data
                            })
                        })
                        .then(res => console.log(res.text()))
                    // .then(res => {
                    //     console.log(res.text())
                    // })
                    // // .then(result => console.log(result))
                    // .catch(err => console.error(err));
                });
        }
        addStudents();
        setInterval(addStudents(), 0);
    </script>
</body>

</html>