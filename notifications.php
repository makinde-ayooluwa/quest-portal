<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Fetch notifications for the current student
$query = "SELECT n.*, nr.read_at as user_read_at FROM notifications n LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.user_type = 'student' AND nr.user_id = :user_id ORDER BY n.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $studentData['id']);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark notifications as read when viewed (only for this user)
$insertQuery = "INSERT IGNORE INTO notification_reads (notification_id, user_type, user_id) SELECT id, 'student', :user_id FROM notifications WHERE id NOT IN (SELECT notification_id FROM notification_reads WHERE user_type = 'student' AND user_id = :user_id)";
$insertStmt = $pdo->prepare($insertQuery);
$insertStmt->bindParam(":user_id", $studentData['id']);
$insertStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Quest Schools</title>
    <!-- Bootstrap CSS -->
    <!--Fonts-->
    <link rel="stylesheet" href="css/fonts.min.css">
    <!--Favicon-->
    <link rel="shortcut icon" href="assets/images/Quest logo.jpg" type="image/x-icon">
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

        body {
            background: #f8f9fa;
        }

        .notification-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-left: 4px solid #0d6efd;
        }

        .notification-card.unread {
            border-left-color: #198754;
            background: #f8fff9;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .notification-icon.info {
            background: #e7f3ff;
            color: #0d6efd;
        }

        .notification-icon.success {
            background: #e8f5e8;
            color: #198754;
        }

        .notification-icon.warning {
            background: #fff3cd;
            color: #ffc107;
        }

        .notification-icon.error {
            background: #f8d7da;
            color: #dc3545;
        }

        .notification-time {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .no-notifications {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>

    <div class="container-fluid px-4 py-4" style="margin-left: 250px;">
    <style>
        @media (max-width: 768px) {
            .container-fluid {
                margin-left: 0 !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }
        @media (max-width: 576px) {
            .notification-card {
                padding: 1rem !important;
            }
            .notification-icon {
                width: 35px !important;
                height: 35px !important;
            }
            .notification-time {
                font-size: 0.8rem !important;
            }
        }
    </style>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Notifications</h2>
            <span class="badge bg-primary"><?php echo count($notifications); ?> total</span>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="no-notifications">
                <i class="bi bi-bell-slash fs-1 text-muted mb-3"></i>
                <h4>No notifications yet</h4>
                <p>You'll see your notifications here when you receive them.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card <?php echo $notification['user_read_at'] ? '' : 'unread'; ?>">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon <?php echo $notification['type']; ?>">
                            <?php
                            switch ($notification['type']) {
                                case 'success':
                                    echo '<i class="bi bi-check-circle-fill"></i>';
                                    break;
                                case 'warning':
                                    echo '<i class="bi bi-exclamation-triangle-fill"></i>';
                                    break;
                                case 'error':
                                    echo '<i class="bi bi-x-circle-fill"></i>';
                                    break;
                                default:
                                    echo '<i class="bi bi-info-circle-fill"></i>';
                            }
                            ?>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                            <p class="mb-2 text-muted"><?php echo htmlspecialchars($notification['message']); ?></p>
                            <div class="notification-time">
                                <?php
                                $createdAt = new DateTime($notification['created_at']);
                                echo $createdAt->format('M j, Y \a\t g:i A');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
