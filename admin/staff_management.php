<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Users Management - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .staff-card {
            max-width: 1100px;
            margin: 3rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
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

        .mentor-badge {
            background: #fec511;
            color: #212529;
            font-weight: 500;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.95em;
        }

        .admin-badge {
            background: #0d6efd;
            color: #fff;
            font-weight: 500;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.95em;
        }

        .teacher-badge {
            background: #198754;
            color: #fff;
            font-weight: 500;
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.95em;
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <div class="staff-card container">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="mb-0"><i class="bi bi-people-fill me-2"></i>Users Management</h3>
                        <div>
                            <?php
                            if ($adminData['staff_role'] == "head admin") {
                            ?>
                                <a href="add_staff.php" class="btn btn-primary btn-sm me-2"><i class="bi bi-person-plus"></i> Add User</a>
                            <?php
                            }
                            ?>
                            <!-- <a href="upload_material.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-cloud-upload"></i> Upload Material</a> -->
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form class="d-flex" id="staffSearchForm">
                                <input class="form-control me-2" type="search" placeholder="Search user by name, role, email..." aria-label="Search" id="staffSearchInput">
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php
                    if (isset($_SESSION["success"])) {
                    ?>
                        <script>
                            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
                        </script>
                    <?php
                        unset($_SESSION["success"]);
                    } elseif (isset($_SESSION["error"])) {
                    ?>
                        <script>
                            toastr.error("<?php echo $_SESSION["error"] ?>", "Error!");
                        </script>
                    <?php
                        unset($_SESSION["error"]);
                    }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:64px">Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Portal Code</th>
                                    <th>Verification</th>
                                    <th style="width:110px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM staffs;";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($result as $row) {
                                ?>
                                    <tr>
                                        <td>
                                            <img width="48" class="rounded-circle border border-1" src="<?php echo $row["picture"]; ?>" alt="">
                                        </td>
                                        <td><?php echo $row["fullname"] ?>
                                            <span class="badge bg-primary ms-2"><?php echo strtoupper($row["staff_role"]) ?></span>
                                        </td>
                                        <td>
                                            <?php echo $row["email"]  ?>
                                        </td>
                                        <td>
                                            <?php echo $row["phone"] ?>
                                        </td>
                                        <td>
                                            <?php echo $row["portal_code"] ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row["account_verification"] == "Verified") {
                                            ?>
                                                <span class="badge bg-success"><?php echo $row["account_verification"] ?></span>
                                            <?php
                                            } else {
                                            ?>
                                                <span class="badge bg-danger"><?php echo $row["account_verification"] ?></span>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($row["staff_role"] == "head admin") {
                                            ?>
                                                <?php
                                            } elseif ($adminData["staff_role"] == "head admin") {
                                                if ($row["staff_role"] == "admin" || $row["staff_role"] == "retention officer" || $row["staff_role"] == "assessment officer" || $row["staff_role"] == "teacher") {
                                                ?>
                                                    <a title="View Staff" href="view_staff.php?id=<?php echo $row["id"] ?>" class="btn btn-sm btn-outline-info me-1" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="bi bi-eye"></i></a>
                                                    <button title="Delete Staff" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal_for_staff_<?php echo $row["id"] ?>" aria-label="Delete"><i class="bi bi-trash"></i></button>
                                            <?php
                                                }
                                            }
                                            ?>

                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modal_for_staff_<?php echo $row["id"] ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-danger fs-5">Are you sure you want to delete <?php echo "<b>" . strtoupper($row["fullname"]) . "</b>"; ?> as a staff?</p>
                                                    <p>This action is going to delete all data about the <?php echo "<b>" . strtoupper($row["fullname"]) . "</b>"; ?> and all the classes assigned to <?php echo "<b>" . strtoupper($row["fullname"]) . "</b>"; ?>.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="delete_staff.php?id=<?php echo $row["id"] ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!--<div class="mt-4">
                        <h5><i class="bi bi-bar-chart-line me-2"></i>Staff Performance & Attendance</h5>
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Attendance (%)</th>
                                    <th>Performance Rating</th>
                                    <th>Last Evaluation</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>John Doe</td>
                                    <td>95%</td>
                                    <td><span class="badge bg-success">Excellent</span></td>
                                    <td>2025-09-18</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-5">
                            <h5><i class="bi bi-journal-richtext me-2"></i>Content & Resource Distribution</h5>
                            <?php
                            /*if (isset($_SESSION["upload_error"])) {
                                echo '<div class="alert alert-success">' . $_SESSION["upload_error"] . '</div>';
                                unset($_SESSION["upload_error"]);
                            } elseif (isset($_SESSION["upload_success"])) {
                                echo '<div class="alert alert-success">' . $_SESSION["upload_success"] . '</div>';
                                unset($_SESSION["upload_success"]);
                            }*/
                            ?>
                            <form action="upload_material.php" method="post" enctype="multipart/form-data"
                                class="row g-3 align-items-center mb-3" id="upload_material">
                                <div class="col-md-4">
                                    <label for="materialTitle" class="form-label">Material Title</label>
                                    <input type="text" class="form-control" id="materialTitle" name="materialTitle" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="materialType" class="form-label">Type</label>
                                    <select class="form-select" id="materialType" name="materialType" required>
                                        <option value="">Select Type</option>
                                        <option value="LEMA">LEMA</option>
                                        <option value="Guide">Guide</option>
                                        <option value="Notes">Notes</option>
                                        <option value="Worksheet">Worksheet</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="materialFile" class="form-label">Upload File</label>
                                    <input type="file" class="form-control" id="materialFile" name="materialFile"
                                        accept=".pdf,.doc,.docx,.ppt,.jpg,.png" required>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-grad"><i class="bi bi-cloud-upload me-1"></i>Upload
                                        Material</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Uploaded By</th>
                                            <th>Date</th>
                                            <th>Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        /*$query = "SELECT * FROM materials;";
                                        $stmt = $pdo->prepare($query);
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        if (empty($result)) {
                                            echo '<tr><td colspan="5" class="text-center">No materials uploaded yet.</td></tr>';
                                        } else {
                                            foreach ($result as $row) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $row["materialTitle"] ?></td>
                                                    <td><?php echo $row["materialType"] ?></td>
                                                    <td><?php
                                                        $query = "SELECT * FROM staffs WHERE email = :email;";
                                                        $stmt = $pdo->prepare($query);
                                                        $stmt->bindParam(":email", $row["uploaded_by"]);
                                                        $stmt->execute();
                                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                        echo $result["fullname"];
                                                        ?></td>
                                                    <td><?php echo $row["uploaded_on"] ?></td>
                                                    <td>
                                                        <a href="assets/uploads/materials/<?php echo $row["materialFile"] ?>" download class="btn btn-sm btn-success">
                                                            <i class="bi bi-download"></i> <?php echo strtoupper(pathinfo($row["materialFile"], PATHINFO_EXTENSION)) ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        }*/
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>-->
                </div>

                <script>
                    document.querySelector('.staff-card form').addEventListener('input', function(e) {
                        e.preventDefault();
                        const query = this.querySelector('input[type="search"]').value.toLowerCase();
                        const rows = document.querySelectorAll('.staff-card table tbody tr');
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(query) ? '' : 'none';
                        });
                    });
                </script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                            new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    });
                </script>
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