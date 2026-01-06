<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Fetch all events with read status for the current student, ordered by date
$query = "SELECT e.*, er.read_at as user_read_at FROM events e LEFT JOIN events_read er ON e.id = er.event_id AND er.user_type = 'student' AND er.user_id = :user_id ORDER BY e.event_date DESC, e.start_time DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $studentData['id']);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark events as read when viewed (only for this user)
$insertQuery = "INSERT IGNORE INTO events_read (event_id, user_type, user_id) SELECT id, 'student', :user_id FROM events WHERE id NOT IN (SELECT event_id FROM events_read WHERE user_type = 'student' AND user_id = :user_id)";
$insertStmt = $pdo->prepare($insertQuery);
$insertStmt->bindParam(":user_id", $studentData['id']);
$insertStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Quest Schools</title>
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

        .event-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-left: 4px solid #0d6efd;
        }

        .event-card.upcoming {
            border-left-color: #198754;
            background: #f8fff9;
        }

        .event-card.past {
            border-left-color: #6c757d;
            opacity: 0.7;
        }

        .event-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background: #e7f3ff;
            color: #0d6efd;
        }

        .event-date {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .event-time {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .event-location {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .no-events {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .event-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .event-status.upcoming {
            background: #d1ecf1;
            color: #0c5460;
        }

        .event-status.past {
            background: #f8f9fa;
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
            .event-card {
                padding: 1rem !important;
            }
            .event-icon {
                width: 40px !important;
                height: 40px !important;
            }
        }
    </style>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Events</h2>
            <span class="badge bg-primary"><?php echo count($events); ?> total</span>
        </div>

        <?php if (empty($events)): ?>
            <div class="no-events">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                <h4>No events yet</h4>
                <p>You'll see upcoming events here when they are scheduled.</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <?php
                $eventDateTime = new DateTime($event['event_date'] . ' ' . $event['start_time']);
                $now = new DateTime();
                $isUpcoming = $eventDateTime > $now;
                $statusClass = $isUpcoming ? 'upcoming' : 'past';
                $statusText = $isUpcoming ? 'Upcoming' : 'Past';
                ?>
                <div class="event-card <?php echo $statusClass; ?> position-relative">
                    <span class="event-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                    <div class="d-flex align-items-start">
                        <div class="event-icon">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <?php if (!empty($event['description'])): ?>
                                <p class="mb-2 text-muted"><?php echo htmlspecialchars($event['description']); ?></p>
                            <?php endif; ?>
                            <div class="event-date">
                                <i class="bi bi-calendar me-1"></i>
                                <?php
                                $eventDate = new DateTime($event['event_date']);
                                echo $eventDate->format('l, F j, Y');
                                ?>
                            </div>
                            <div class="event-time">
                                <i class="bi bi-clock me-1"></i>
                                <?php
                                $startTime = date('g:i A', strtotime($event['start_time']));
                                $endTime = date('g:i A', strtotime($event['end_time']));
                                echo $startTime . ' - ' . $endTime;
                                ?>
                            </div>
                            <div class="event-location">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?php echo htmlspecialchars($event['location']); ?>
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
