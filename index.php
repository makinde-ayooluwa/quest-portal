<?php

session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';
include "student_includes/student.inc.php";
define("STUDENT_DATA", $studentData);

// Fetch unread notifications count
$unreadQuery = "SELECT COUNT(*) as unread_count FROM notifications n LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.user_type = 'student' AND nr.user_id = :user_id WHERE nr.read_at IS NULL";
$unreadStmt = $pdo->prepare($unreadQuery);
$unreadStmt->bindParam(":user_id", $studentData['id']);
$unreadStmt->execute();
$unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

// Fetch upcoming events count
$upcomingEventsQuery = "SELECT COUNT(*) as upcoming_count FROM events WHERE event_date >= CURDATE()";
$upcomingEventsStmt = $pdo->prepare($upcomingEventsQuery);
$upcomingEventsStmt->execute();
$upcomingEventsCount = $upcomingEventsStmt->fetch(PDO::FETCH_ASSOC)['upcoming_count'];

// Fetch assignment stats
$assignmentQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
    FROM (
        SELECT a.*, 
        CASE WHEN asub.submitted_at IS NOT NULL THEN 'submitted' ELSE 'pending' END as status
        FROM assignments a 
        LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id AND asub.student_id = :student_id
        WHERE a.class_name = :class_name
    ) t";
$assignmentStmt = $pdo->prepare($assignmentQuery);
$assignmentStmt->bindParam(":student_id", $studentData['id']);
$assignmentStmt->bindParam(":class_name", $studentData['class']);
$assignmentStmt->execute();
$assignmentStats = $assignmentStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> <?php echo $studentData['fullname'] ?> | Quest Schools - Student Portal</title>
    <?php include "head.php" ?>
    <style>
        .hero-section {
            background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-green-400) 40%, var(--quest-yellow) 100%);
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease forwards;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .welcome-toast {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--radius-full);
            padding: 0.5rem 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            animation: fadeInDown 0.5s ease 0.2s both;
        }

        .stat-card-dashboard {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--slate-100);
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease forwards;
        }

        .stat-card-dashboard:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card-dashboard:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card-dashboard:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card-dashboard:nth-child(4) {
            animation-delay: 0.4s;
        }

        .stat-card-dashboard:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card-dashboard::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, transparent 50%, rgba(90, 172, 123, 0.06) 50%);
            border-radius: 0 0 0 100%;
        }

        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon-wrap.green {
            background: var(--quest-green-100);
            color: var(--quest-green-600);
        }

        .stat-icon-wrap.yellow {
            background: var(--quest-yellow-100);
            color: var(--quest-yellow-600);
        }

        .stat-icon-wrap.blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .stat-icon-wrap.purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .action-card {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--slate-100);
            transition: all var(--transition-base);
            height: 100%;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease forwards;
        }

        .action-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .action-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .action-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .action-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .action-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        .action-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
            border-color: var(--quest-green-200);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--transition-base);
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            transition: transform var(--transition-bounce);
        }

        .action-card:hover .action-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .action-icon.primary {
            background: var(--quest-green-100);
            color: var(--quest-green-600);
        }

        .action-icon.success {
            background: #d1fae5;
            color: #059669;
        }

        .action-icon.info {
            background: #e0f2fe;
            color: #0284c7;
        }

        .action-icon.warning {
            background: var(--quest-yellow-100);
            color: var(--quest-yellow-600);
        }

        .action-icon.dark {
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-base);
            margin-top: 1rem;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--slate-200), transparent);
            margin-left: 0.5rem;
        }

        .recent-activity {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--slate-100);
            animation: fadeInUp 0.5s ease 0.3s both;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--slate-100);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 0.5rem;
            flex-shrink: 0;
        }

        .activity-dot.green {
            background: var(--quest-green);
            box-shadow: 0 0 0 4px var(--quest-green-100);
        }

        .activity-dot.yellow {
            background: var(--quest-yellow);
            box-shadow: 0 0 0 4px var(--quest-yellow-100);
        }

        .activity-dot.blue {
            background: var(--sky-500);
            box-shadow: 0 0 0 4px #e0f2fe;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <?php include "header.php" ?>
    <?php include "sidebar.php" ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-9 main-bar">
                <!-- Hero Section -->
                <div class="hero-section mt-4">
                    <div class="hero-content">
                        <div class="welcome-toast">
                            <i class="bi bi-stars"></i>
                            <span>Welcome back, <?php echo $studentData["fullname"]; ?>!</span>
                        </div>
                        <h1 class="text-white mb-2" style="font-weight: 800; font-size: 2rem;">
                            <?php echo $studentData['fullname']; ?>
                        </h1>
                        <p class="text-white mb-0" style="opacity: 0.9; font-size: 1.1rem;">
                            Class: <span class="fw-bold" style="color: #fff;"><?php echo STUDENT_DATA["class"]; ?></span>
                        </p>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card-dashboard">
                            <div class="stat-icon-wrap green">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <div class="stat-value text-2xl font-bold text-slate-800"><?php echo $unreadCount; ?></div>
                            <div class="stat-label text-sm text-slate-500">Unread Notifications</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card-dashboard">
                            <div class="stat-icon-wrap yellow">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <div class="stat-value text-2xl font-bold text-slate-800"><?php echo $upcomingEventsCount; ?></div>
                            <div class="stat-label text-sm text-slate-500">Upcoming Events</div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card-dashboard">
                            <div class="stat-icon-wrap blue">
                                <i class="bi bi-journal-check"></i>
                            </div>
                            <div class="stat-value text-2xl font-bold text-slate-800"><?php echo $assignmentStats['submitted'] ?? 0; ?></div>
                            <div class="stat-label text-sm text-slate-500">Assignments Done</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="stat-card-dashboard">
                            <div class="stat-icon-wrap purple">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="stat-value text-2xl font-bold text-slate-800"><?php echo $assignmentStats['pending'] ?? 0; ?></div>
                            <div class="stat-label text-sm text-slate-500">Pending Tasks</div>
                        </div>
                    </div> -->
                </div>

                <!-- Quick Actions -->
                <div class="section-title">
                    <i class="bi bi-grid-3x3-gap-fill text-green"></i>
                    Quick Actions
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="action-card">
                            <div class="action-icon primary">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">Profile Management</h5>
                            <p class="text-slate-500 text-sm mb-0">View and edit your personal details, update contact information and guardian details.</p>
                            <a href="profile_edit.php" class="action-btn" style="background: var(--quest-green-100); color: var(--quest-green-700);">
                                <i class="bi bi-pencil"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="action-card">
                            <div class="action-icon success">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">Academic Records</h5>
                            <p class="text-slate-500 text-sm mb-0">Access your results, grades, and attendance reports all in one place.</p>
                            <a href="result.php" class="action-btn" style="background: #d1fae5; color: #059669;">
                                <i class="bi bi-bar-chart"></i> View Results
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="action-card">
                            <div class="action-icon info">
                                <i class="bi bi-bell-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">Notifications</h5>
                            <p class="text-slate-500 text-sm mb-0">Stay updated with exam schedules, events, competitions, and school announcements.</p>
                            <a href="notifications.php" class="action-btn" style="background: #e0f2fe; color: #0284c7;">
                                <i class="bi bi-bell"></i> View Notifications
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="action-card">
                            <div class="action-icon warning">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">Events</h5>
                            <p class="text-slate-500 text-sm mb-0">Check out upcoming school events, competitions, and extracurricular activities.</p>
                            <a href="events.php" class="action-btn" style="background: var(--quest-yellow-100); color: var(--quest-yellow-600);">
                                <i class="bi bi-calendar"></i> View Events
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="action-card">
                            <div class="action-icon dark">
                                <i class="bi bi-question-circle-fill"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2">Support / Help Desk</h5>
                            <p class="text-slate-500 text-sm mb-0">Raise requests or concerns to admin. We're here to help!</p>
                            <a href="support.php" class="action-btn" style="background: var(--slate-100); color: var(--slate-700);">
                                <i class="bi bi-envelope"></i> Get Support
                            </a>
                        </div>
                    </div>
                </div>
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