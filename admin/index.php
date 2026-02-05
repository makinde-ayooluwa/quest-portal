<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_SESSION["admin"])) {
  header("Location: login.php");
  exit();
}

//PHPMailer
/*require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
$mail = new PHPMailer(true); // true enables exceptions for error handling
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com'; // Or your SMTP server host
$mail->SMTPAuth   = true;
$mail->Username   = 'makindeayooluwa604@gmail.com';
$mail->Password   = 'lirw zgkb kegs xyat'; // Use an app password for Gmail
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // or PHPMailer::ENCRYPTION_SMTPS
$mail->Port       = 587; // or 465 for SMTPS
$mail->setFrom('makindeayooluwa604@gmail.com', 'Makinde Ayooluwa');
$mail->addAddress('makindeayooluwa42@gmail.com', 'Makinde Ayooluwa');
// Optional: addReplyTo, addCC, addBCC
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = 'Subject of your email';
$mail->Body    = 'This is the <b>HTML body</b> of the email.';
$mail->AltBody = 'This is the plain text body for non-HTML mail clients.';
//$mail->addAttachment('/vendor/phpmailer/docs/README.md', 'document.pdf');
try {
    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}*/

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Admin Dashboard - QUEST PORTAL</title>
  <?php include "head.php" ?>
  <style>
    * {
      font-family: Montserrat;
    }

    body {
      background: #f8f9fa;
    }


    .main-content {
      margin-left: 220px;
      padding: 2rem 1rem 1rem 1rem;
      transition: margin-left 0.3s;
    }

    .stat-card {
      width: 90%;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      padding: 1rem;
      margin-bottom: 1rem;
      text-align: center;
    }

    /*.dashboard .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }*/

    .recent-activity ul {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      padding: 1rem;
      margin-top: 1rem;
    }

    .recent-activity ul li {
      margin-bottom: 0.5rem;
    }

    .assignments-table,
    .notifications-list {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      padding: 1rem;
      margin-top: 1rem;
    }

    .progress {
      height: 7.5px;
    }

    /* Intro System Styles */
    .info-card {
      width: 300px;
      min-height: 150px;
      background-color: #fff;
      border: 2px solid #007bff;
      padding: 15px 15px 15px 35px;
      /* Increased left padding */
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      position: absolute;
      z-index: 10000;
      display: none;
      border-radius: 8px;
      transition: box-shadow 0.3s ease;
    }

    .info-card:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .highlight {
      border: 3px solid #007bff !important;
      box-shadow: 0 0 15px rgba(0, 123, 255, 0.5) !important;
      position: relative;
      z-index: 9999;
    }

    .next-btn,
    .skip-btn {
      margin: 5px;
      padding: 8px 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .skip-btn {
      background-color: #6c757d;
    }

    .next-btn:hover {
      background-color: #0056b3;
    }

    .skip-btn:hover {
      background-color: #545b62;
    }

    /* Responsive styles for info-card */
    @media (max-width: 768px) {
      .info-card {
        width: 90vw;
        max-width: 280px;
        min-height: 120px;
        padding: 10px 15px;
        font-size: 14px;
        left: 5vw !important;
        /* Ensure it's not at the edge */
        right: 5vw !important;
      }

      .info-card h4 {
        font-size: 16px;
        margin-bottom: 8px;
      }

      .info-card ul {
        margin: 8px 0;
      }

      .info-card ul li {
        font-size: 14px;
        margin-bottom: 4px;
      }

      .next-btn,
      .skip-btn {
        font-size: 12px;
        padding: 6px 10px;
      }
    }

    @media (max-width: 480px) {
      .info-card {
        width: 95vw;
        max-width: 260px;
        padding: 8px 12px;
        font-size: 13px;
      }

      .info-card h4 {
        font-size: 15px;
      }

      .info-card ul li {
        font-size: 13px;
      }
    }
  </style>
</head>

<body>
  <?php include "header_sidebar.php" ?>
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
      toastr.success("<?php echo $_SESSION["success"] ?>", "Success!")
    </script>
  <?php
    unset($_SESSION["success"]);
  }
  ?>
  <main class="main-content">
    <section class="dashboard">
      <!-- Quick Actions Panel -->
      <div class="quick-actions mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 id="greeting" class="h3">Welcome, <span
              class="text-uppercase fw-bolder"><?php echo $adminData["fullname"] ?></span>.</h2>
          <div class="d-flex flex-column flex-sm-row gap-2">
            <?php
            if ($adminData["staff_role"] == "head admin") {
            ?>
              <button class="btn btn-primary btn-sm" onclick="window.location.href='add_staff.php'">
                <i class="bi bi-person-badge"></i> Add User
              </button>
            <?php
            }
            ?>
            <button class="btn btn-warning btn-sm" onclick="window.location.href='add_notification.php'">
              <i class="bi bi-bell"></i> Add Notification
            </button>
            <button class="btn btn-info btn-sm" onclick="window.location.href='add_event.php'">
              <i class="bi bi-calendar-event"></i> Add Event
            </button>
          </div>
        </div>
      </div>
      <style>
        .shaker {
          animation: shake 2s linear infinite;
          position: absolute
        }

        @keyframes shake {
          from {
            padding-bottom: 0px;
          }

          to {
            padding-bottom: 15px;
          }
        }

        .quick-actions {
          background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-yellow) 100%);
          border-radius: 12px;
          padding: 1.5rem;
          color: white;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .quick-actions .btn {
          border-radius: 8px;
          font-weight: 500;
          transition: all 0.3s ease;
        }

        .quick-actions .btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .col-md {
          grid-template-rows: auto;
        }
      </style>
      <!-- Stats Cards with Notifications -->
      <div class="stats row justify-content-center mb-4">
        <div class="stat-card col-md-6 col-lg">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h3><i class="bi bi-people-fill px-3"></i>Total Students</h3>
              <canvas id="studentsVerificationChart"></canvas>
            </div>
            <?php
            if ($admin->getStudents($pdo)) {
            ?>
              <span
                class="badge m-0 bg-danger rounded-pill"><?php echo count($admin->getStudents($pdo)) ?></span>
            <?php
            }
            ?>
          </div>
        </div>
        <?php
        if ($adminData['staff_role'] == "head admin") {
        ?>
          <div class="stat-card col-md-6 col-lg">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h3><i class="bi bi-people-fill px-3"></i>Total Users</h3>
                <canvas id="staffsVerificationChart"></canvas>
              </div>
              <span class="badge m-0 bg-danger rounded-pill"><?php echo count($admin->getStaffs($pdo)) ?></span>
            </div>
          </div>
        <?php
        }
        ?>
        <!-- <div class="stat-card col-md-6 col-lg">
          <h3><i class="bi bi-house-door-fill px-3"></i>Total Classes</h3>
          <h1 class="fs-2">
            <?php echo count($classes); ?>
          </h1>
          <div class="progress mt-3">
            <div class="progress-bar bg-warning" style="width: <?php echo min((count($classes)) * 100, 100); ?>%;">
            </div>
          </div>
        </div> -->
        <!-- System Health Card -->
        <div class="stat-card col-md">
          <h3><i class="bi bi-shield-check px-3"></i>System Health</h3>
          <div class="mt-3">
            <div class="d-flex justify-content-between mb-2">
              <small>Database:</small>
              <small
                class="text-success"><?php echo isset($systemHealth['db_status']) ? $systemHealth['db_status'] : 'Unknown'; ?></small>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <small>Size:</small>
              <small><?php echo isset($systemHealth['db_size']) ? $systemHealth['db_size'] : 'Unknown'; ?></small>
            </div>
            <div class="d-flex justify-content-between">
              <small>Logs (24h):</small>
              <small><?php echo isset($systemHealth['recent_logs']) ? $systemHealth['recent_logs'] : '0'; ?></small>
            </div>
          </div>
        </div>
        <div class="stat-card col-md-12" id="classes">
          <?php
          if ($adminData["staff_role"] == "head admin") {
          ?>
            <section class="mt-4">
              <h2>Classes</h2>
              <div class="card p-3 mb-3">
                <div class="d-flex mb-3 align-items-center gap-2">
                  <input id="classSearch" class="form-control form-control-sm"
                    placeholder="Search classes by name or teacher">
                  <div class="ms-auto text-muted small">Showing <?php echo count($classes); ?> classes</div>
                </div>
                <div class="table-responsive">
                  <table id="classesTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                      <tr>
                        <th style="width:48px">#</th>
                        <th>Class Name</th>
                        <th style="width:260px">Teacher(s)</th>
                        <th style="width:120px">Students</th>
                        <th style="width:120px">Status</th>
                        <th style="width:140px">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($classes)) { ?>
                        <tr>
                          <td colspan="6" class="text-center py-4">No classes found.</td>
                        </tr>
                        <?php } else {
                        $i = 1;
                        foreach ($classes as $class) {
                          $cname = $class['class_name'];
                          // get class rows to find mentors
                          $stmt = $pdo->prepare('SELECT * FROM classes WHERE class_name = :class_name');
                          $stmt->bindValue(':class_name', $cname);
                          $stmt->execute();
                          $classRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                          $mentorNames = [];
                          foreach ($classRows as $r) {
                            if (!empty($r['mentor_name']))
                              $mentorNames[] = $r['mentor_name'];
                          }
                          $mentorNames = array_values(array_unique($mentorNames));

                          // resolve mentor displays
                          $mentorHtml = [];
                          foreach ($mentorNames as $mn) {
                            // try to find staff by email
                            $s = $pdo->prepare('SELECT fullname,picture FROM staffs WHERE email = :email LIMIT 1');
                            $s->bindValue(':email', $mn);
                            $s->execute();
                            $staff = $s->fetch(PDO::FETCH_ASSOC);
                            if ($staff) {
                              $pic = htmlspecialchars($staff['picture'] ?? '');
                              $mentorHtml[] = '<div class="d-flex align-items-center mb-1"><img src="' . $pic . '" width="36" height="36" class="rounded-circle me-2" alt="">' . htmlspecialchars($staff['fullname']) . '</div>';
                            } else {
                              $mentorHtml[] = '<div class="mb-1">' . htmlspecialchars($mn) . '</div>';
                            }
                          }

                          // student count
                          $sStmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE class = :class');
                          $sStmt->bindValue(':class', $cname);
                          $sStmt->execute();
                          $studentCount = (int) $sStmt->fetchColumn();

                          $status = isset($class['class_status']) ? $class['class_status'] : '';
                        ?>
                          <tr data-class-name="<?php echo htmlspecialchars(strtolower($cname)); ?>"
                            data-mentors="<?php echo htmlspecialchars(implode(',', $mentorNames)); ?>">
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($cname); ?></td>
                            <td><?php echo implode('', $mentorHtml); ?></td>
                            <td><b class="fs-5"><?php echo $studentCount; ?></b></td>
                            <td><?php if ($status === 'Active') {
                                  echo '<span class="badge bg-success">Active</span>';
                                } elseif ($status) {
                                  echo '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
                                } else {
                                  echo '<span class="badge bg-light text-dark">-</span>';
                                } ?></td>
                            <td>
                              <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-primary"
                                title="Edit"><i class="bi bi-pencil"></i></a>
                              <a href="view_class.php?id=<?php echo $class['id']; ?>"
                                class="btn btn-sm btn-outline-secondary ms-1" title="View"><i class="bi bi-eye"></i></a>
                            </td>
                          </tr>
                      <?php }
                      } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <script>
                document.getElementById('classSearch')?.addEventListener('input', function(e) {
                  const q = e.target.value.toLowerCase();
                  document.querySelectorAll('#classesTable tbody tr').forEach(function(row) {
                    const name = row.getAttribute('data-class-name') || '';
                    const mentors = row.getAttribute('data-mentors') || '';
                    row.style.display = (name.includes(q) || mentors.includes(q)) ? '' : 'none';
                  });
                });
              </script>
            </section>
          <?php
          }
          ?>
          <section class="mt-4">
            <h2><i class="bi bi-activity me-2"></i>Recent Activities</h2>
            <div class="card">
              <div class="card-body">
                <?php if (isset($recentActivities) && !empty($recentActivities)): ?>
                  <div class="activity-feed">
                    <?php foreach ($recentActivities as $activity): ?>
                      <div class="activity-item d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="activity-icon me-3">
                          <i class="bi bi-circle-fill text-<?php
                                                            echo $activity['user_type'] === 'admin' ? 'primary' : ($activity['user_type'] === 'staff' ? 'success' : 'info');
                                                            ?>"></i>
                        </div>
                        <div class="activity-content flex-grow-1">
                          <div class="d-flex justify-content-between align-items-start">
                            <div>
                              <strong><?php echo htmlspecialchars($activity['action']); ?></strong>
                              <?php if ($activity['details']): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($activity['details']); ?></small>
                              <?php endif; ?>
                            </div>
                            <small class="text-muted">
                              <?php echo date('M d, H:i', strtotime($activity['timestamp'])); ?>
                            </small>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">No recent activities to display.</p>
                <?php endif; ?>
              </div>
            </div>
          </section>

          <!-- Notifications Section -->
          <section class="mt-4">
            <h2><i class="bi bi-bell me-2"></i>Recent Notifications</h2>
            <div class="card">
              <div class="card-body">
                <?php if (isset($notifications) && !empty($notifications)): ?>
                  <div class="notification-feed">
                    <?php foreach ($notifications as $notification): ?>
                      <div class="notification-item d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="notification-icon me-3">
                          <i class="bi bi-circle-fill text-<?php
                                                            echo $notification['type'] === 'error' ? 'danger' : ($notification['type'] === 'warning' ? 'warning' : 'info');
                                                            ?>"></i>
                        </div>
                        <div class="notification-content flex-grow-1">
                          <div class="d-flex justify-content-between align-items-start">
                            <div>
                              <strong><?php echo htmlspecialchars($notification['title']); ?></strong>
                              <br><small
                                class="text-muted"><?php echo htmlspecialchars($notification['message']); ?></small>
                            </div>
                            <small class="text-muted">
                              <?php echo date('M d, H:i', strtotime($notification['created_at'])); ?>
                            </small>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">No recent notifications.</p>
                <?php endif; ?>
              </div>
            </div>
          </section>

          <!-- Upcoming Events Section -->
          <section class="mt-4">
            <h2><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h2>
            <div class="card">
              <div class="card-body">
                <?php if (isset($upcomingEvents) && !empty($upcomingEvents)): ?>
                  <div class="event-feed">
                    <?php foreach ($upcomingEvents as $event): ?>
                      <div class="event-item d-flex align-items-start mb-3 pb-3 border-bottom">
                        <div class="event-icon me-3">
                          <i class="bi bi-calendar-check text-primary"></i>
                        </div>
                        <div class="event-content flex-grow-1">
                          <div class="d-flex justify-content-between align-items-start">
                            <div>
                              <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                              <?php if ($event['description']): ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars($event['description']); ?></small>
                              <?php endif; ?>
                              <br><small class="text-muted">
                                <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['location']); ?> |
                                <i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($event['start_time'])); ?> -
                                <?php echo date('H:i', strtotime($event['end_time'])); ?>
                              </small>
                            </div>
                            <small class="text-muted">
                              <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                            </small>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="text-muted mb-0">No upcoming events.</p>
                <?php endif; ?>
              </div>
            </div>
          </section>
        </div>
      </div>
    </section>

    <section class="mt-4">
      <h2>Admin Features</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-person-lines-fill me-2"></i>Student Management</h5>
              <ul class="mb-0">
                <!-- <li>Add, edit, and track student records across schools.</li> -->
                <li>View student profiles and academic history.</li>
                <li>Search and filter by class, school, or status.</li>
              </ul>
              <a href="students.php" class="btn btn-primary btn-sm mt-2"><i class="bi bi-people"></i> View Students</a>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-calendar-check-fill me-2"></i>Attendance & Academic
                Monitoring</h5>
              <ul class="mb-0">
                <li>Upload results and attendance records.</li>
                <li>Generate assessment and attendance reports.</li>
                <li>Track academic progress and trends.</li>
              </ul>
              <a class="btn btn-warning btn-sm mt-2" href="manage_results.php"><i class="bi bi-upload"></i> Upload Results</a>
            </div>
          </div>
        </div>
        <!-- <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-journal-richtext me-2"></i>Content & Resource Distribution
              </h5>
              <ul class="mb-0">
                <li>Upload study materials, guides, and LEMA resources.</li>
                <li>Share resources with students and staff.</li>
                <li>Manage downloadable content and access.</li>
              </ul>
              <button class="btn btn-info btn-sm mt-2" disabled>
                <a href="upload_material.php"><i class="bi bi-cloud-upload"></i> Upload
                  Material</a>
              </button>
            </div>
          </div>
        </div> -->
        <!-- <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-award-fill me-2"></i>Scholarship Tracking</h5>
              <ul class="mb-0">
                <li>Manage scholarship applications and approvals.</li>
                <li>Track awardees and scholarship status.</li>
                <li>Generate reports for scholarship distribution.</li>
              </ul>
              <button class="btn btn-secondary btn-sm mt-2" disabled><i class="bi bi-search"></i> View
                Applications</button>
            </div>
          </div>
        </div> -->
      </div>
    </section>
  </main>
  <?php include "footer.php" ?>
  <script>
    // Prevent right-click context menu
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
    });

    // Intro System Functions
    let currentCard = null;
    let currentSectionParent = null;

    function pauseTimeout() {
      clearTimeout(introTimeout);
    }

    function resumeTimeout() {
      introTimeout = setTimeout(function() {
        nextIntro();
      }, 8000);
    }

    function sections() {
      return [{
          "title": "Navigation Header",
          "functions": [
            "Contains the QUEST logo and branding",
            "Global search bar for finding students, staff, and classes",
            "Admin profile dropdown with account options"
          ],
          "parent": document.querySelector("header"),
          "parentLocation": document.querySelector("header").getBoundingClientRect()
        },
        {
          "title": "Sidebar Navigation",
          "functions": [
            "Collapsible menu with dashboard sections",
            "Quick access to Students, Staff, and System Management",
            "Profile section with account settings"
          ],
          "parent": document.querySelector("#sidebar"),
          "parentLocation": document.querySelector("#sidebar").getBoundingClientRect()
        },
        {
          "title": "Sidebar Toggle",
          "functions": [
            "Hamburger menu button to show/hide sidebar on mobile",
            "Responsive design for smaller screens"
          ],
          "parent": document.querySelector("#sidebarToggle"),
          "parentLocation": document.querySelector("#sidebarToggle").getBoundingClientRect()
        },
        {
          "title": "Welcome Greeting",
          "functions": [
            "Personalized welcome message with admin name",
            "Quick overview of the dashboard"
          ],
          "parent": document.querySelector("#greeting"),
          "parentLocation": document.querySelector("#greeting").getBoundingClientRect()
        },
        {
          "title": "Quick Actions Panel",
          "functions": [
            "Add new students, staff, notifications, and events",
            "Access common administrative tasks quickly"
          ],
          "parent": document.querySelector(".quick-actions"),
          "parentLocation": document.querySelector(".quick-actions").getBoundingClientRect()
        },
        {
          "title": "Statistics Cards",
          "functions": [
            "View total students, staff, classes, and system health",
            "Monitor key metrics and notifications"
          ],
          "parent": document.querySelector(".stats"),
          "parentLocation": document.querySelector(".stats").getBoundingClientRect()
        },
        {
          "title": "Classes Table",
          "functions": [
            "Search and filter classes by name or teacher",
            "View class details, student counts, and status",
            "Edit or view individual classes"
          ],
          "parent": document.querySelector("#classes"),
          "parentLocation": document.querySelector("#classes").getBoundingClientRect()
        },
        {
          "title": "Admin Features",
          "functions": [
            "Manage students, staff, attendance, and content",
            "Access various administrative tools and reports"
          ],
          "parent": document.querySelectorAll("section")[document.querySelectorAll("section").length - 1],
          "parentLocation": document.querySelectorAll("section")[document.querySelectorAll("section").length - 1].getBoundingClientRect()
        }
      ];
    }

    function displayCard(index) {
      let section = sections()[index];
      let card = document.createElement("div");
      card.className = "info-card";
      card.id = "info-card-" + index;

      // Get fresh bounding rect after scrolling
      let rect = section.parent.getBoundingClientRect();

      // Calculate position to avoid overflow
      let cardWidth = 300;
      let cardHeight = 200; // Approximate min height
      let top = rect.top + window.scrollY + 10;
      let left = rect.left + rect.width + 10;

      // Adjust left if it would overflow right edge
      if (left + cardWidth > window.innerWidth + window.scrollX) {
        left = rect.left - cardWidth + 500;
      }

      // Adjust top if it would overflow bottom edge
      if (top + cardHeight > window.innerHeight + window.scrollY) {
        top = rect.top + window.scrollY - cardHeight - 10;
      }

      card.style.top = top + "px";
      card.style.left = left + "px";

      // Scroll the page to the card's position
      window.scrollTo({
        top: top - 50,
        behavior: 'smooth'
      });

      let title = document.createElement("h4");
      title.innerText = section.title;
      card.appendChild(title);

      let funcList = document.createElement("ul");
      section.functions.forEach(function(func) {
        let funcItem = document.createElement("li");
        funcItem.innerText = func;
        funcList.appendChild(funcItem);
      });
      card.appendChild(funcList);

      let btnContainer = document.createElement("div");
      btnContainer.style.textAlign = "right";

      let skipBtn = document.createElement("button");
      skipBtn.className = "skip-btn";
      skipBtn.innerText = "Skip Intro";
      skipBtn.onclick = function() {
        skipIntro();
      };
      btnContainer.appendChild(skipBtn);

      let nextBtn = document.createElement("button");
      nextBtn.className = "next-btn";
      nextBtn.innerText = index === sections().length - 1 ? "Finish" : "Next";
      nextBtn.onclick = function() {
        nextIntro();
      };
      btnContainer.appendChild(nextBtn);

      card.appendChild(btnContainer);

      document.body.appendChild(card);
      card.style.display = "block";

      // Ensure the entire card is visible by scrolling into view if necessary
      card.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
        inline: 'nearest'
      });

      // Store current card and section for timeout management
      currentCard = card;
      currentSectionParent = section.parent;

      // Add hover event listeners to pause/resume timeout on the info card and highlighted element
      card.addEventListener('mouseenter', pauseTimeout);
      card.addEventListener('mouseleave', resumeTimeout);

      // Also add to the highlighted parent element
      section.parent.addEventListener('mouseenter', pauseTimeout);
      section.parent.addEventListener('mouseleave', resumeTimeout);
    }

    function completeIntro() {
      // Clear timeout
      clearTimeout(introTimeout);

      // Remove event listeners from current card and section
      if (currentCard) {
        currentCard.removeEventListener('mouseenter', pauseTimeout);
        currentCard.removeEventListener('mouseleave', resumeTimeout);
      }
      if (currentSectionParent) {
        currentSectionParent.removeEventListener('mouseenter', pauseTimeout);
        currentSectionParent.removeEventListener('mouseleave', resumeTimeout);
      }

      // Remove all highlights
      sections().forEach(function(sec) {
        sec.parent.classList.remove("highlight");
      });

      // Hide all cards
      for (let i = 0; i < sections().length; i++) {
        let card = document.getElementById("info-card-" + i);
        if (card) {
          card.style.display = "none";
        }
      }

      // Mark as shown
      localStorage.setItem('introShown', 'true');
    }

    let currentIntroIndex = 0;
    let introTimeout;

    function introduce() {
      if (currentIntroIndex < sections().length) {
        // Highlight the current section
        let section = sections()[currentIntroIndex];
        section.parent.classList.add("highlight");

        // Scroll to the section
        section.parent.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });

        // Display the card
        displayCard(currentIntroIndex);

        // Auto-advance after 8 seconds
        introTimeout = setTimeout(function() {
          nextIntro();
        }, 8000);
      } else {
        // Intro finished, remove highlights
        sections().forEach(function(sec) {
          sec.parent.classList.remove("highlight");
        });
        localStorage.setItem('introShown', 'true');
      }
    }

    function nextIntro() {
      // Clear timeout
      clearTimeout(introTimeout);

      // Remove event listeners from current card and section
      if (currentCard) {
        currentCard.removeEventListener('mouseenter', pauseTimeout);
        currentCard.removeEventListener('mouseleave', resumeTimeout);
      }
      if (currentSectionParent) {
        currentSectionParent.removeEventListener('mouseenter', pauseTimeout);
        currentSectionParent.removeEventListener('mouseleave', resumeTimeout);
      }

      // Remove highlight from current section
      if (currentIntroIndex < sections().length) {
        sections()[currentIntroIndex].parent.classList.remove("highlight");
      }

      // Hide current card
      let currentCardElement = document.getElementById("info-card-" + currentIntroIndex);
      if (currentCardElement) {
        currentCardElement.style.display = "none";
      }

      // Move to next
      currentIntroIndex++;
      introduce();
    }

    function skipIntro() {
      // Clear timeout
      clearTimeout(introTimeout);

      // Remove event listeners from current card and section
      if (currentCard) {
        currentCard.removeEventListener('mouseenter', pauseTimeout);
        currentCard.removeEventListener('mouseleave', resumeTimeout);
      }
      if (currentSectionParent) {
        currentSectionParent.removeEventListener('mouseenter', pauseTimeout);
        currentSectionParent.removeEventListener('mouseleave', resumeTimeout);
      }

      // Remove all highlights
      sections().forEach(function(sec) {
        sec.parent.classList.remove("highlight");
      });

      // Hide all cards
      for (let i = 0; i < sections().length; i++) {
        let card = document.getElementById("info-card-" + i);
        if (card) {
          card.style.display = "none";
        }
      }

      // Mark as shown
      localStorage.setItem('introShown', 'true');
    }

    // Check if intro should be shown
    document.addEventListener("DOMContentLoaded", function() {
      if (!localStorage.getItem('introShown')) {
        setTimeout(function() {
          introduce();
        }, 1000); // Delay to ensure page is fully loaded
      }
    });

    // Reset intro on page unload (simulate closing/reopening)
    // window.addEventListener('beforeunload', function() {
    //   localStorage.removeItem('introShown');
    // });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctxs = [{
        title: "students",
        ctx: document.getElementById("studentsVerificationChart"),
        verification_data_url: "get_student_verification.php"
      },
      {
        title: "users",
        ctx: document.getElementById("staffsVerificationChart"),
        verification_data_url: "get_staffs_verification.php"
      }
    ];
    ctxs.map(ctx => {
      const chart = new Chart(ctx.ctx, {
        type: 'pie',
        data: {
          labels: ['Verified', 'Not Verified'],
          datasets: [{
            data: [0, 0], // start empty
            backgroundColor: ['green', 'red'],
            hoverOffset: 20
          }]
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: `${ctx.title.toLocaleUpperCase()} VERIFICATION RATE`
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw;
                  return `${label}: ${value} ${ctx.title}`;
                }
              }
            }
          }
        }
      });

      // ✅ Fetch function that updates the chart
      function fetchData() {
        fetch(ctx.verification_data_url)
          .then(response => response.json())
          .then(data => {
            chart.data.datasets[0].data = [data.verified, data.unverified];
            chart.update();
          })
          .catch(error => console.error('Error fetching data:', error));
      }

      // Initial fetch
      fetchData();

      // Refresh every 5 seconds
      setInterval(fetchData, 5000);
    })
  </script>
</body>

</html>