<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
$classes = $admin->getClasses($pdo);
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
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>
    <?php
    if (isset($_SESSION["success"])) {
    ?>
        <script>
            toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>", "Success!")
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
                        <style>
                            body[data-theme='dark']>* {
                                color: #fff;
                            }

                            body[data-theme='dark'] .students-card {
                                background: #000;
                                box-shadow: 0 4px 15px rgb(255, 255, 255, 0.2);
                            }

                            body[data-theme='dark'] .students-toolbar form input {
                                background: #000;
                                box-shadow: 0 4px 15px rgb(255, 255, 255, 0.2);
                            }

                            body[data-theme='dark'] .students-toolbar form input::placeholder {
                                color: #fff;
                            }
                        </style>
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
                    </div>

                    <form id="bulkActionsForm" method="post" action="bulk_student_actions.php">
                        <!-- <div class="d-flex align-items-center gap-2 mb-2">
                            <select name="action" id="bulkActionSelect" class="form-select form-select-sm" style="width:220px">
                                <option value="">Bulk actions</option>
                                <option value="delete">Delete selected</option>
                                <option value="promote">Promote / Demote selected</option>
                            </select>
                            <input type="hidden" name="promote_to" id="promoteToInput" value="">
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                            <div class="ms-auto text-muted small">Select rows and choose an action</div>
                        </div> -->
                        <div class="d-flex align-items-center justify-content-start">
                            <button class="btn border btn-outline-success rounded-circle fw-bolder" title="Refresh students table" type="button" id="refreshStudentsButton"><i class="bi bi-arrow-clockwise"></i></button>
                        </div>
                        <div class="d-flex align-items-center justify-content-start gap-2 w-50 p-3">
                            <p>Sort</p>
                            <select name="orderSelector" id="orderSelector" class="form-select">
                                <option value="fullname">By Fullname</option>
                                <option value="class">By Class</option>
                                <option value="account_verification">By Verification</option>
                                <option value="admission_number">By Admission Number</option>
                            </select>
                            <select name="modeSelector" id="modeSelector" class="form-select">
                                <option value="ASC">A - Z</option>
                                <option value="DESC">Z - A</option>
                            </select>
                        </div>

                        <div style="overflow-x:scroll;">
                            <style>
                                .students-table {
                                    width: 100%;
                                }

                                .students-table thead,
                                th,
                                tr,
                                td {
                                    border-top: 1px solid #000;
                                    border-right: 1px solid #000;
                                    border-collapse: collapse;
                                    padding: 5px;
                                }

                                body[data-theme='dark'] .students-table thead,
                                body[data-theme='dark'] .students-table th,
                                body[data-theme='dark'] .students-table tr,
                                body[data-theme='dark'] .students-table td {
                                    border-color: #fff;
                                }
                            </style>
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <!--<th style="width:40px"><input type="checkbox" id="selectAll"></th>-->
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
                                <tbody style="overflow-y: scroll;" id="studentsTable">

                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <!-- Promote Modal -->
                <!-- <div class="modal fade" id="promoteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Promote / Demote selected students</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-2">Select destination class:</div>
                                <select id="promoteClassSelect" class="form-select">
                                    <?php /*foreach ($classes as $c) {
                                        echo '<option value="' . htmlspecialchars($c["class_name"]) . '">' . htmlspecialchars($c["class_name"]) . '</option>';
                                    }*/ ?>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="confirmPromoteBtn" class="btn btn-primary btn-sm">Promote / Demote</button>
                            </div>
                        </div>
                    </div>
                </div> -->
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
        function fetchData(order, mode) {
            fetch("ajax_order_data_for_students.php", {
                    method: "POST",
                    headers: {
                        "Content-type": "application/json"
                    },
                    body: JSON.stringify({
                        order: order,
                        mode: mode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    const studentsTable = document.getElementById("studentsTable");
                    studentsTable.innerHTML = ""; // clear table first
                    let html = "";

                    if (!data || data.length < 1 || data.length == 0) {
                        html = `
                            <tr>
                                <td colspan="9" class="text-center py-4">No students found.</td>
                            </tr>`;

                    }



                    data.forEach(student => {
                        html += `
                            <tr>
                                <!--<td>
                                    <input type="checkbox" name="selected_ids[]" value="${student.id}" class="rowCheckbox">
                                </td>-->

                                <td>
                                    <img src="../${student.picture}" alt="${student.fullname}" class="student-photo">
                                </td>

                                <td>${student.fullname}</td>
                                <td>${student.class}</td>
                                <td>${student.email}</td>
                                <td>${student.phone}</td>
                                <td>${student.admission_number}</td>

                                <td>
                                    ${
                                        student.account_verification === "Verified"
                                            ? '<span class="badge bg-success">Verified</span>'
                                            : '<span class="badge bg-danger text-white">Not verified</span>'
                                    }
                                </td>

                                <td class="text-nowrap">
                                    <a href="view_student.php?id=${student.id}" 
                                    class="btn btn-sm btn-outline-primary p-1 me-1">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a onclick="this.innerHTML = '<span>Sending...</span>'; this.disabled" href="send_mail.php?id=${student.id}"
                                    class="btn btn-sm btn-outline-danger p-1">
                                        <span>Send Setup Mail</span>
                                    </a>
                                </td>
                            </tr>

                            <!-- Promote Modal -->

                            <!-- Delete Modal -->
                            <!--<div class="modal fade" id="modal_${student.id}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger">
                                                Are you sure you want to delete 
                                                <strong>${student.fullname.toUpperCase()}</strong>?
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <a href="delete_student.php?id=${student.id}" class="btn btn-danger">
                                                Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                        `;
                    });
                    studentsTable.innerHTML = html;
                })
        }
        const orderSelector = document.getElementById("orderSelector");
        const modeSelector = document.getElementById("modeSelector");
        const currentOrder = localStorage.getItem("sortingOrder") ? JSON.parse(localStorage.getItem("sortingOrder")) : {
            "order": "fullname",
            "mode": "ASC"
        };
        orderSelector.value = currentOrder.order;
        modeSelector.value = currentOrder.mode;
        orderSelector.addEventListener("change", function() {
            currentOrder.order = orderSelector.value;
            fetchData(currentOrder.order, currentOrder.mode);
            localStorage.setItem("sortingOrder", JSON.stringify(currentOrder));
        })
        modeSelector.addEventListener("change", function() {
            currentOrder.mode = modeSelector.value;
            fetchData(currentOrder.order, currentOrder.mode);
            localStorage.setItem("sortingOrder", JSON.stringify(currentOrder));
        })
        document.addEventListener("DOMContentLoaded", () => {
            fetchData(currentOrder.order, currentOrder.mode);
        });
        document.getElementById("refreshStudentsButton").addEventListener("click", function() {
            fetchData(currentOrder.order, currentOrder.mode);
        });
    </script>
</body>

</html>