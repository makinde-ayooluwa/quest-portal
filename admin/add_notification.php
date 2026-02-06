<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Notification - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .add-card {
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

    <div class="add-card">
        <h2 class="mb-4 text-center"><i class="bi bi-bell me-2"></i>Add New Notification</h2>
        <form action="add_notification_handler.php" method="post">
            <div class="row mb-3">
                <div class="col-md-12 mb-2">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" rows="4" required></textarea>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type" required>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                        <option value="success">Success</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="./" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-grad"><i class="bi bi-bell"></i> Add Notification</button>
            </div>
        </form>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    </script>
</body>

</html>