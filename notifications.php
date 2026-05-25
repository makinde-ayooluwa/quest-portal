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
    <title>Notifications - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        .notification-card {
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-left: 4px solid var(--sky-500);
            transition: all var(--transition-base);
            animation: fadeInUp 0.4s ease forwards;
            color: #000;
        }
        .notification-card > *{
            color: #000;
        }

        .notification-card:nth-child(1) { animation-delay: 0.05s; }
        .notification-card:nth-child(2) { animation-delay: 0.1s; }
        .notification-card:nth-child(3) { animation-delay: 0.15s; }
        .notification-card:nth-child(4) { animation-delay: 0.2s; }
        .notification-card:nth-child(5) { animation-delay: 0.25s; }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .notification-card.unread {
            border-left-color: var(--quest-green);
            background: var(--quest-green-50);
        }

        .notification-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.1rem;
        }

        .notification-icon.info {
            background: #e0f2fe;
            color: var(--sky-500);
        }

        .notification-icon.success {
            background: #d1fae5;
            color: var(--emerald-500);
        }

        .notification-icon.warning {
            background: var(--quest-yellow-100);
            color: var(--quest-yellow-600);
        }

        .notification-icon.error {
            background: #fee2e2;
            color: var(--rose-500);
        }

        .notification-time {
            font-size: 0.875rem;
            color: var(--slate-500);
        }

        .no-notifications {
            text-align: center;
            padding: 3rem;
            color: var(--slate-500);
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 py-4">
                <div class="page-header">
                    <h2 class="mb-0 fw-bold"><i class="bi bi-bell-fill text-green me-2"></i>Notifications</h2>
                    <span class="badge badge-modern badge-secondary"><?php echo count($notifications); ?> total</span>
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
                            <p class="mb-2"><?php echo htmlspecialchars($notification['message']); ?></p>
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
        </div>
    </div>

    <script>
        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>
