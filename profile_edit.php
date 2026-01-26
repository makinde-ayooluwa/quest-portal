<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "head.php" ?>
    <title>Edit Profile - Quest Schools</title>

    <style>
        :root {
            --quest-yellow: #fec511;
            --quest-green: #5aac7b;
        }


        body {
            background: #f8f9fa;
        }

        .edit-card {
            max-width: 700px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .profile-avatar {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d6efd;
        }

        .section-title {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>
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
            toastr.success("<?php echo $_SESSION["success"] ?>", "Success!");
        </script>
    <?php
    }
    ?>
    <div class="edit-card">
        <div class="d-flex align-items-center mb-4">
            <img src="
            <?php
            echo $studentData['picture'];
            // Example: echo "SS2A";
            ?>
            " alt="Student Avatar" class="profile-avatar me-3">
            <div>
                <h2 class="mb-0">Edit Profile</h2>
                <p class="mb-0 text-muted">Class: <span class="fw-bold">
                        <?php
                        echo $studentData['class'];
                        // Example: echo "SS2A";
                        ?>
                    </span></p>
            </div>
        </div>
        <form action="profile_edit_handler.php" method="POST" enctype="multipart/form-data">
            <h4 class="section-title">Personal Details</h4>
            <script>
                setTimeout(() => {
                    <?php
                    unset($_SESSION['error']);
                    unset($_SESSION['success']);
                    ?>
                }, 0);
            </script>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option selected><?php echo $studentData['gender']; ?></option>
                        <option><?php
                                switch ($studentData['gender']) {
                                    case "Male":
                                        echo "Female";
                                        break;
                                    case "Female":
                                        echo "Male";
                                        break;
                                }
                                ?></option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Phone</label>
                    <input name="phone" type="tel" class="form-control" value="<?php echo $studentData['phone']; ?>">
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Address</label>
                    <input name="home_address" type="text" class="form-control"
                        value="<?php echo $studentData['home_address']; ?>">
                </div>
            </div>
            <h4 class="section-title">Parent/Guardian Information</h4>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Father's Name</label>
                    <input name="father_name" type="text" class="form-control"
                        value="<?php echo $studentData['father_name']; ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Father's Phone</label>
                    <input name="father_phone" type="tel" class="form-control"
                        value="<?php echo $studentData['father_phone']; ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Father's Email</label>
                    <input name="father_email" type="email" class="form-control"
                        value="<?php echo $studentData['father_email']; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Mother's Name</label>
                    <input name="mother_name" type="text" class="form-control"
                        value="<?php echo $studentData['mother_name']; ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Mother's Phone</label>
                    <input name="mother_phone" type="tel" class="form-control"
                        value="<?php echo $studentData['mother_phone']; ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Mother's Email</label>
                    <input name="mother_email" type="email" class="form-control"
                        value="<?php echo $studentData['mother_email']; ?>">
                </div>
            </div>
            <div class="modal fade" id="confirmModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Changes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger fs-5">Enter your password to save changes</p>
                            <p>For your account security, password is required to make changes in your profile.</p>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input autofocus name="password" type="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save
                                Changes</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="profile.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#confirmModal" class="btn btn-success"><i
                        class="bi bi-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
    <script>
        const toast = document.getElementById("custom-toast");
        const loader = toast.querySelector(".toast-loader");
        let fadeTimeout;

        function showToast() {
            toast.classList.add("visible");
            loader.style.animationPlayState = "running";

            fadeTimeout = setTimeout(() => {
                toast.classList.remove("visible");
            }, 4000);
        }

        toast.addEventListener("mouseover", () => {
            clearTimeout(fadeTimeout);
            loader.style.animationPlayState = "paused";
        });

        toast.addEventListener("mouseout", () => {
            loader.style.animationPlayState = "running";
            fadeTimeout = setTimeout(() => {
                toast.style.opacity = "0";
            }, 2000);
        });

        showToast();
    </script>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>