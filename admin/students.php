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
        .main-content {
            margin-left: 220px; padding: 2rem 1rem;
        }
        .students-card {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            animation: fadeInUp 0.5s ease forwards;
        }
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; }
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
            overflow-x: scroll;
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
    <div class="container-fluid main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="students-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="mb-0 fw-bold"><i class="bi bi-people text-green me-2"></i>Student Management</h3>
                    </div>

                    <div class="students-toolbar">
                        <form class="d-flex students-search" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search students by name, email or admission number" aria-label="Search" id="studentSearch">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                    </div>

                    <form id="bulkActionsForm" method="post" action="bulk_student_actions.php">
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
                                    border-top: 1px solid var(--border);
                                    border-right: 1px solid var(--border);
                                    border-collapse: collapse;
                                    padding: 5px;
                                }
                            </style>
                            <table class="students-table table-responsive">
                                <thead>
                                    <tr>
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
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
    <script>
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
                    studentsTable.innerHTML = "";
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
