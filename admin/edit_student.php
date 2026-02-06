<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

function studentIsNotGotten($pdo, $id)
{
    $query = "SELECT * FROM students WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        return true;
    } else {
        return false;
    }
}
if (!isset($_GET["id"])) {
?>
    <title>Error - Quest Schools Admin</title>
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/quest.jpg" type="image/x-icon">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=fire">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">-->
    <!--Styles-->
    <link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!--Scripts-->
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        * {
            font-family: Montserrat;
        }
    </style>
    <style>
        .error-container {
            max-width: 500px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff3f3;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        .error-container h2 {
            color: #d9534f;
            margin-bottom: 1rem;
        }

        .error-container p {
            color: #555;
            margin-bottom: 1.5rem;
        }

        .error-container a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #d9534f;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .error-container a:hover {
            background: #c9302c;
        }
    </style>
    <div class="error-container">
        <h2><i class="bi bi-shield-fill-exclamation"></i></h2>
        <p>Sorry, the method used to request this page is not allowed.</p>
        <a href="./">Go to Homepage</a>
    </div>
<?php
} else if (studentIsNotGotten($pdo, $_GET["id"])) {
?>
    <title>Error - Quest Schools Admin</title>
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/quest.jpg" type="image/x-icon">
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Trirong">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=fire">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia&effect=neon|outline|emboss|shadow-multiple">-->
    <!--Styles-->
    <link rel="stylesheet" href="bootstrap5/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!--Scripts-->
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <style>
        * {
            font-family: Montserrat;
        }
    </style>
    <style>
        .error-container {
            max-width: 500px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff3f3;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        }

        .error-container h2 {
            color: #d9534f;
            margin-bottom: 1rem;
        }

        .error-container p {
            color: #555;
            margin-bottom: 1.5rem;
        }

        .error-container a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #d9534f;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .error-container a:hover {
            background: #c9302c;
        }
    </style>
    <div class="error-container">
        <h2><i class="bi bi-shield-fill-exclamation"></i></h2>
        <p>Sorry, the method used to request this page is not allowed.</p>
        <a href="./">Go to Homepage</a>
    </div>
<?php
} else {
?>
    <?php
    $query = "SELECT * FROM students WHERE id = :id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":id", $_GET["id"]);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION["edit_student_id"] = $result["id"];
    define("STUDENT_DATA", $result);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Edit Student - Quest Schools Admin</title>
        <?php include "head.php" ?>
        <style>
            * {
                font-family: Montserrat;
            }

            body {
                background: #f8f9fa;
            }

            .edit-card {
                max-width: 700px;
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
        </style>
    </head>

    <body>
    <?php include "settings.php" ?>
        <?php include "header_sidebar.php" ?>
        <?php
        if (isset($_SESSION['error'])) {
        ?>
            <!--<div class="error-loader error">
            <div class="d-flex justify-content-start p-2">
                <div class="text-white p-1">
                    <i class="bi bi-shield-fill-exclamation display-4"></i>
                </div>
                <div class="d-grid">
                    <span class="text-start fw-bold fs-4 text-white">Error</span>
                    <span class="text-white fs-6"><?php echo $_SESSION["error"] ?></span>
                </div>
            </div>
            <div class="loader">
                <div class="loader-bar"></div>
            </div>
            <script>
                const loader = document.querySelector(".error-loader");
                setTimeout(() => {
                    $(".error-loader").css("left", "74%");
                }, 10);
                setTimeout(() => {
                    $(".error-loader").css("left", "100%");
                }, 2500);
                setTimeout(() => {
                    document.body.removeChild(loader);
                }, 3900);
            </script>
        </div>-->
            <div id="custom-toast" class="toast-container">
                <div class="toast-message">
                    <i class="bi bi-shield-fill-exclamation fs-3"></i>
                    <span class="text-break"><strong class="fs-3">Error!</strong><br> <?php echo $_SESSION["error"] ?></span>
                    <div class="toast-loader"></div>
                </div>
            </div>
            <style>
                .toast-container {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    opacity: 0;
                    transition: opacity 0.5s ease-in-out;
                    pointer-events: none;
                }

                .toast-container.visible {
                    opacity: 1;
                    pointer-events: auto;
                }

                .toast-message {
                    align-items: center;
                    gap: 10px;
                    background: #ff00008f;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    font-family: Montserrat, sans-serif;
                    position: relative;
                }

                .toast-loader {
                    z-index: 20;
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    height: 4px;
                    width: 100%;
                    background-color: rgba(255, 255, 255, 1);
                    border-bottom-left-radius: 8px;
                    border-bottom-right-radius: 8px;
                    animation: loadBar 4s linear forwards;
                    animation-play-state: running;
                }

                @keyframes loadBar {
                    from {
                        width: 100%;
                    }

                    to {
                        width: 0%;
                    }
                }
            </style>
            <script>
                const toasts = document.getElementById("custom-toast");
                const loaders = toast.querySelector(".toast-loader");
                let fadeTimeouts;

                function showToast() {
                    toasts.classList.add("visible");
                    loaders.style.animationPlayState = "running";

                    fadeTimeouts = setTimeout(() => {
                        toasts.classList.remove("visible");
                    }, 4000);
                }

                toasts.addEventListener("mouseover", () => {
                    clearTimeout(fadeTimeouts);
                    loaders.style.animationPlayState = "paused";
                });

                toasts.addEventListener("mouseout", () => {
                    loaders.style.animationPlayState = "running";
                    fadeTimeouts = setTimeout(() => {
                        toasts.style.opacity = "0";
                    }, 2000);
                });

                showToast();
            </script>
        <?php
            unset($_SESSION['error']);
        } else if (isset($_SESSION['success'])) {
        ?>
            <div id="custom-toast" class="toast-container">
                <div class="toast-message">
                    <i class="bi bi-shield-check fs-3"></i>
                    <span class="text-break"><strong class="fs-3">Success!</strong><br> <?php echo $_SESSION["success"] ?></span>
                    <div class="toast-loader"></div>
                </div>
            </div>
            <style>
                .toast-container {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    opacity: 0;
                    transition: opacity 0.5s ease-in-out;
                    pointer-events: none;
                }

                .toast-container.visible {
                    opacity: 1;
                    pointer-events: auto;
                }

                .toast-message {
                    align-items: center;
                    gap: 10px;
                    background: #08b03eff;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    font-family: Montserrat, sans-serif;
                    position: relative;
                }

                .toast-loader {
                    z-index: 20;
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    height: 4px;
                    width: 100%;
                    background-color: rgba(255, 255, 255, 0.4);
                    border-bottom-left-radius: 8px;
                    border-bottom-right-radius: 8px;
                    animation: loadBar 4s linear forwards;
                    animation-play-state: running;
                }

                @keyframes loadBar {
                    from {
                        width: 100%;
                    }

                    to {
                        width: 0%;
                    }
                }
            </style>
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
        <?php
            unset($_SESSION['success']);
        }
        ?>
        <div class="edit-card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Student Information</h2>
                <small class="text-light">Update student details below</small>
            </div>
            <div class="card-body">
                <!-- Current Profile Preview -->
                <div class="text-center mb-4">
                    <div class="profile-preview">
                        <img src="<?php echo htmlspecialchars(STUDENT_DATA['picture'] ?? 'assets/images/no-picture.jpg'); ?>" alt="Current Profile" class="rounded-circle border" width="100" height="100">
                        <p class="mt-2 text-muted">Current Profile Picture</p>
                    </div>
                </div>

                <form action="edit_student_handler.php" method="post" enctype="multipart/form-data">
                    <!-- Personal Details Section -->
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-person-fill me-2"></i>Personal Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" name="fullname" value="<?php echo htmlspecialchars(STUDENT_DATA['fullname']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                    <select class="form-select" name="gender" required>
                                        <option value="Male" <?php echo (STUDENT_DATA['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo (STUDENT_DATA['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo (STUDENT_DATA['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars(STUDENT_DATA['email']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Admission Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(STUDENT_DATA['admission_number']); ?>" readonly>
                                </div>
                                <small class="text-muted">Admission number cannot be changed</small>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Picture Section 
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-camera-fill me-2"></i>Profile Picture</h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Upload New Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="picture" accept=".jpg,.jpeg,.png,.gif">
                                <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="upload_image_later" id="upload_image_later">
                                    <label class="form-check-label" for="upload_image_later">
                                        Keep current picture
                                    </label>
                                </div>
                            </div>
                        </div>
                        <script>
                            const profileImage = document.querySelector("input#profile_picture");
                            const checkbox = document.querySelector("#upload_image_later");
                            checkbox.addEventListener("change", function() {
                                if (checkbox.checked) {
                                    profileImage.setAttribute("disabled", true);
                                    profileImage.value = "";
                                } else {
                                    profileImage.removeAttribute("disabled");
                                }
                            });
                        </script>
                    </div>-->

                    <!-- Academic Details Section -->
                    <div class="section-card mb-4">
                        <h5 class="section-title"><i class="bi bi-book-fill me-2"></i>Academic Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Current Class <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                                    <select name="class" id="class" class="form-select" required>
                                        <?php
                                        $query = "SELECT class_name FROM classes_names_only;";
                                        $stmt = $pdo->prepare($query);
                                        $stmt->execute();
                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            $selected = (STUDENT_DATA['class'] === $row['class_name']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($row['class_name']) . "' $selected>" . htmlspecialchars($row['class_name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Account Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars(STUDENT_DATA['account_verification']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div class="text-muted">
                            <small><i class="bi bi-info-circle me-1"></i>Fields marked with <span class="text-danger">*</span> are required</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="students.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-grad">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    </body>

    </html>
<?php
}
?>