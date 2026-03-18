<!--<link rel="stylesheet" href="https://unpkg.com/tailwindcss/dist/
tailwind.min.css">-->
<style>
    #searchSuggestions {
        background-color: #fff;
        position: absolute;
        left: 25%;
        z-index: 20;
        width: 30%;
        top: 10%;
        text-align: center;
        text-transform: capitalize;
    }

    .dropdown-menu {
        position: absolute !important;
        top: 100%;
        left: auto;
        right: 0;
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        min-width: 320px;
    }

    @media (max-width: 576px) {
        .dropdown-menu {
            min-width: 250px;
        }
    }
</style>
<div class="container-fluid px-0 sticky-top header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2">
        <div class="container-fluid">

            <div class="d-lg-none toggler-parent">
                <button class="btn px-2 py-2 fw-bolder">
                    <span class="fa fa-bars fs-3"></span>
                </button>
            </div>

            <!-- Left: Logo or Brand -->
            <a class="navbar-brand d-flex align-items-center" href="./">
                <img src="assets/images/quest.jpg" alt="Quest Logo" width="40" class="me-0">
                <!-- <span class="fw-bold text-green"></span> -->
            </a>
            <!-- Center: Search -->
            <form class="d-none d-md-flex mx-auto" style="max-width: 350px;">
                <input class="form-control rounded-pill w-100" type="search" placeholder="Search..." aria-label="Search">
            </form>

            <!-- Search Suggestions -->
            <!-- Right: Profile & Alerts -->
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Events -->
                <li class="nav-item dropdown me-3 d-none d-lg-block">
                    <a class="nav-link position-relative" href="#" id="eventsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-calendar-event fs-5"></i>
                        <?php
                        // Count unread events for current student
                        $unreadEventsQuery = "SELECT COUNT(*) as unread_count FROM events e LEFT JOIN events_read er ON e.id = er.event_id AND er.user_type = 'student' AND er.user_id = :user_id WHERE er.read_at IS NULL";
                        $unreadEventsStmt = $pdo->prepare($unreadEventsQuery);
                        $unreadEventsStmt->bindParam(":user_id", $studentData['id']);
                        $unreadEventsStmt->execute();
                        $unreadEventsCount = $unreadEventsStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
                        if ($unreadEventsCount > 0) {
                            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $unreadEventsCount . '</span>';
                        }
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="eventsDropdown"
                        style="min-width: 320px;">
                        <li class="dropdown-header fw-bold">Upcoming Events</li>
                        <?php
                        // Fetch upcoming events with read status for current student
                        $query = "SELECT e.*, er.read_at as user_read_at FROM events e LEFT JOIN events_read er ON e.id = er.event_id AND er.user_type = 'student' AND er.user_id = :user_id WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC, e.start_time ASC LIMIT 5";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":user_id", $studentData['id']);
                        $stmt->execute();
                        $upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($upcomingEvents)) {
                            echo '<li><a class="dropdown-item text-center text-muted" href="#">No upcoming events</a></li>';
                        } else {
                            foreach ($upcomingEvents as $event) {
                                $eventDate = new DateTime($event['event_date']);
                                $formattedDate = $eventDate->format('M j, Y');
                                $startTime = date('g:i A', strtotime($event['start_time']));
                        ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center <?php echo $event['user_read_at'] ? '' : 'bg-light fw-bold'; ?>" href="events.php">
                                        <i class="bi bi-calendar-event-fill text-primary me-2"></i>
                                        <div class="flex-grow-1">
                                            <div><?php echo htmlspecialchars(substr($event['title'], 0, 50)); ?></div>
                                            <small class="text-muted"><?php echo $formattedDate . ' at ' . $startTime; ?> - <?php echo htmlspecialchars($event['location']); ?></small>
                                        </div>
                                    </a>
                                </li>
                        <?php
                            }
                        }
                        ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-center text-green fw-bold" href="events.php">
                                View all events
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown me-3 d-none d-lg-block">
                    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <?php
                        // Count unread notifications for current student
                        $unreadQuery = "SELECT COUNT(*) as unread_count FROM notifications n LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.user_type = 'student' AND nr.user_id = :user_id WHERE nr.read_at IS NULL";
                        $unreadStmt = $pdo->prepare($unreadQuery);
                        $unreadStmt->bindParam(":user_id", $studentData['id']);
                        $unreadStmt->execute();
                        $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
                        if ($unreadCount > 0) {
                            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $unreadCount . '</span>';
                        }
                        ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="notifDropdown"
                        style="min-width: 320px;">
                        <li class="dropdown-header fw-bold">Notifications</li>
                        <?php
                        // Fetch recent notifications for the current student
                        $query = "SELECT n.*, nr.read_at as user_read_at FROM notifications n LEFT JOIN notification_reads nr ON n.id = nr.notification_id AND nr.user_type = 'student' AND nr.user_id = :user_id ORDER BY n.created_at DESC LIMIT 5";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(":user_id", $studentData['id']);
                        $stmt->execute();
                        $recentNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (empty($recentNotifications)) {
                            echo '<li><a class="dropdown-item text-center text-muted" href="#">No notifications</a></li>';
                        } else {
                            foreach ($recentNotifications as $notification) {
                                $iconClass = 'fa-solid fa-bell text-yellow';
                                switch ($notification['type']) {
                                    case 'success':
                                        $iconClass = 'bi bi-check-circle-fill text-success';
                                        break;
                                    case 'warning':
                                        $iconClass = 'bi bi-exclamation-triangle-fill text-warning';
                                        break;
                                    case 'error':
                                        $iconClass = 'bi bi-x-circle-fill text-danger';
                                        break;
                                    case 'info':
                                    default:
                                        $iconClass = 'bi bi-info-circle-fill text-info';
                                        break;
                                }

                                $createdAt = new DateTime($notification['created_at']);
                                $timeAgo = $createdAt->format('M j, Y g:i A');
                        ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center <?php echo $notification['user_read_at'] ? '' : 'bg-light fw-bold'; ?>" href="notifications.php">
                                        <i class="<?php echo $iconClass; ?> me-2"></i>
                                        <div class="flex-grow-1">
                                            <div><?php echo htmlspecialchars(substr($notification['title'], 0, 50)); ?></div>
                                            <small class="text-muted"><?php echo $timeAgo; ?></small>
                                        </div>
                                    </a>
                                </li>
                        <?php
                            }
                        }
                        ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-center text-green fw-bold" href="notifications.php">
                                View all notifications
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="javascript:;" id="profileDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="
                            <?php
                            echo $studentData['picture'];
                            // Example: echo "SS2A";
                            ?>
                            " alt="Profile" width="32"
                                class="rounded-circle me-2 border">
                            <!-- <span>
                                <?php
                                //echo $studentData["fullname"];
                                ?>
                            </span> -->
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a href="profile_edit.php" class="dropdown-item"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li data-bs-toggle="modal" data-bs-target="#passwordChange"><a href="javascript:void(0);" class="dropdown-item"><i class="bi bi-key me-2"></i>Change password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li data-bs-toggle="modal" data-bs-target="#pictureChange"><a href="javascript:void(0);" class="dropdown-item"><i class="bi bi-pencil me-2"></i>Change Profile Picture</a></li>
                            <!--<li><a class="dropdown-item" href="javascript:;"><i class="bi bi-gear me-2"></i>Settings</a></li>-->
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="sign_out.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
<ul id="searchSuggestions" class="rounded-2 shadow-sm list-group"></ul>
<script>
    const portalSections = [{
            id: "dashboard",
            title: "Dashboard",
            keywords: ["dashboard", "home", "overview", "welcome", "stats"],
            href: "./" // Replace with actual dashboard link if available
        },
        {
            id: "profile",
            title: "My Profile",
            keywords: ["profile", "personal details", "contact info", "guardians", "edit profile"],
            href: "profile.php" // Replace with profile page link
        },
        {
            id: "results",
            title: "My Results / Reports",
            keywords: ["results", "grades", "academic records", "attendance report", "view results"],
            href: "result.php" // Replace with results page link
        },
        {
            id: "assignments",
            title: "Assignments",
            keywords: ["assignments", "homework", "download", "upload", "materials", "LEMA", "course content"],
            href: "assignments.php" // Replace with assignments page link
        },
        {
            id: "notifications",
            title: "Notifications",
            keywords: ["notifications", "alerts", "updates", "events", "competitions", "exam schedule"],
            href: "notifications.php" // Replace with notifications page link
        },
        {
            id: "scholarship",
            title: "Scholarship/Program Status",
            keywords: ["scholarship", "program", "application status", "awardees", "acceptance"],
            href: "javascript:;" // Replace with scholarship page link
        },
        {
            id: "support",
            title: "Help & Support",
            keywords: ["help", "support", "raise request", "concerns", "admin", "mentors", "faq"],
            href: "support.php" // Replace with support/help page link
        },
        {
            id: "events",
            title: "Events",
            keywords: ["events", "event", "upcoming events",],
            href: "events.php" // Replace with support/help page link
        }
    ];

    // Build searchData with reference to section and href
    const searchData = [];
    portalSections.forEach(section => {
        searchData.push({
            label: section.title,
            href: section.href
        });
        section.keywords.forEach(keyword => {
            searchData.push({
                label: keyword,
                href: section.href
            });
        });
    });

    const searchInput = document.querySelector('.navbar input[type="search"]');
    const suggestionsBox = document.getElementById('searchSuggestions');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        suggestionsBox.innerHTML = '';
        if (query.length > 1) {
            const matches = searchData.filter(item => item.label.toLowerCase().includes(query));
            if (matches.length) {
                suggestionsBox.style.display = 'block';
                matches.forEach(match => {
                    const item = document.createElement('a');
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = match.label;
                    item.href = match.href;
                    suggestionsBox.appendChild(item);
                });
            } else {
                suggestionsBox.style.display = 'none';
            }
        } else {
            suggestionsBox.style.display = 'none';
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.style.display = 'none';
        }
    });
    // ...existing code...
</script>
<div class="modal fade" id="passwordChange">
    <form action="change_password.php" method="post" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fs-5">Enter your password to save changes</p>
                    <div class="mb-3">
                        <label class="form-label">Old Password</label>
                        <input name="oldPwd" type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input name="newPwd" type="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input name="confirmPwd" type="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <input type="submit" name="submit" class="btn btn-success" value="Save Changes">
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="pictureChange">
    <form action="change_picture.php" method="post" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green">
                    <h5 class="modal-title">Change Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger fs-5">Choose a picture from your device</p>
                    <div class="mb-3">
                        <label class="form-label">New Picture</label>
                        <input name="picture" type="file" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input name="pwd" type="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <input type="submit" name="submit" class="btn btn-success" value="Save Changes">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
if (isset($_SESSION["error"])) {
?>
    <script>
        toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");
    </script>
<?php
    unset($_SESSION["error"]);
} elseif (isset($_SESSION["success"])) {
?>
    <script>
        toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>", "Success!");
    </script>
<?php
    unset($_SESSION["success"]);
}
?>