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
    <title>Events - Quest Schools</title>
    <?php include "head.php" ?>
    <style>
        .event-card {
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1rem;
            padding: 1.5rem;
            border-left: 4px solid var(--sky-500);
            transition: all var(--transition-base);
            animation: fadeInUp 0.4s ease forwards;
            position: relative;
        }

        .event-card:nth-child(1) { animation-delay: 0.05s; }
        .event-card:nth-child(2) { animation-delay: 0.1s; }
        .event-card:nth-child(3) { animation-delay: 0.15s; }
        .event-card:nth-child(4) { animation-delay: 0.2s; }
        .event-card:nth-child(5) { animation-delay: 0.25s; }

        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .event-card.upcoming {
            border-left-color: var(--quest-green);
            background: var(--quest-green-50);
        }

        .event-card.past {
            border-left-color: var(--slate-400);
            opacity: 0.75;
        }

        .event-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background: #e0f2fe;
            color: var(--sky-500);
            font-size: 1.25rem;
        }

        .event-card.upcoming .event-icon {
            background: var(--quest-green-100);
            color: var(--quest-green-600);
        }

        .event-date {
            font-size: 0.875rem;
            color: var(--slate-500);
            margin-bottom: 0.5rem;
        }

        .event-time {
            font-size: 0.875rem;
            color: var(--slate-500);
        }

        .event-location {
            font-size: 0.875rem;
            color: var(--slate-500);
        }

        .no-events {
            text-align: center;
            padding: 3rem;
            color: var(--slate-500);
        }

        .event-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-weight: 600;
        }

        .event-status.upcoming {
            background: #d1fae5;
            color: var(--quest-green-700);
        }

        .event-status.past {
            background: var(--slate-100);
            color: var(--slate-600);
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
                    <h2 class="mb-0 fw-bold"><i class="bi bi-calendar-event-fill text-green me-2"></i>Events</h2>
                    <span class="badge badge-modern badge-secondary"><?php echo count($events); ?> total</span>
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
