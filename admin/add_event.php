<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Event - Quest Schools Admin</title>
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
                toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>","Error!");
            </script>
        <?php
        unset($_SESSION['error']);
     } else if (isset($_SESSION['success'])) { ?>
            <script>
                toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>","Success!");
            </script>
        <?php
        unset($_SESSION['success']);
         } ?>

    <div class="add-card">
        <h2 class="mb-4 text-center"><i class="bi bi-calendar-event me-2"></i>Add New Event</h2>
        <form action="add_event_handler.php" method="post">
            <div class="row mb-3">
                <div class="col-md-12 mb-2">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Event Date</label>
                    <input type="date" class="form-control" name="event_date" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Start Time</label>
                    <input type="time" class="form-control" name="start_time" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">End Time</label>
                    <input type="time" class="form-control" name="end_time" required>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="./" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-grad"><i class="bi bi-calendar-event"></i> Add Event</button>
            </div>
        </form>
    </div>
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
