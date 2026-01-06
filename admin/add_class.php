<?php
session_start();

include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Class - Admin</title>
    <?php include "head.php" ?>
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include "header_sidebar.php" ?>
    <div class="form-container container">
        <h2>Add Class</h2>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        } else {
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
        } ?>
        <form action="add_class_handler.php" method="POST">
            <div class="mb-3">
                <label for="class_name" class="form-label">Class Name</label>
                <input type="text" class="form-control" id="class_name" name="class_name" required>
            </div>
            <div class="mb-3">
                <label for="mentor_name" class="form-label">Mentor Name</label>
                <select name="mentor_name" id="mentor_name" class="form-select" required>
                    <option value="">Select Mentor</option>
                    <?php
                    // Fetch mentors from the database
                    $query = "SELECT * FROM staffs WHERE staff_role = 'Mentor' AND staff_status = 'Active' AND assigned_class = 'None' OR staff_role = 'Teacher' AND staff_status = 'Active' AND assigned_class = 'None';";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($mentors)) {
                        echo '<option value="" disabled>No available mentors or teachers</option>';
                    } else {
                        foreach ($mentors as $mentor) {
                            echo '<option value="' . $mentor['email'] . '">' . $mentor['fullname'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <!--<div class="mb-3">
                <label for="no_of_students" class="form-label">No. of Students</label>
                <input type="number" class="form-control" id="no_of_students" name="no_of_students" required>
            </div>-->
            <div class="mb-3">
                <label for="class_status" class="form-label">Class Status</label>
                <select name="class_status" id="class_status" class="form-select" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Class</button>
        </form>
    </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>