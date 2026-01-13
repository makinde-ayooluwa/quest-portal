<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

/*if (!isset($_SESSION["teacher"])) {
    header("Location: login.php");
    exit();
}*/

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$assignedClasses = $teacher->getAssignedClasses($pdo, $email);
$allClasses = $teacher->getAllClasses($pdo);

// Get total students across assigned classes
$totalStudents = 0;
foreach ($assignedClasses as $class) {
    $students = $teacher->getStudentsInClass($pdo, $class['class_name']);
    $totalStudents += count($students);
}

// Get assignments for the teacher
$assignments = [];
foreach ($assignedClasses as $class) {
    $classAssignments = $teacher->getAssignmentsForClass($pdo, $class['class_name'], $teacherData['id']);
    $assignments = array_merge($assignments, $classAssignments);
}
$assignments = array_unique($assignments, SORT_REGULAR); // Remove duplicates if any

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Teacher Dashboard - QUEST PORTAL</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }



        .stat-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
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

        .table-responsive {
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
            padding: 15px 15px 15px 35px; /* Increased left padding */
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
                left: 5vw !important; /* Ensure it's not at the edge */
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
            .next-btn, .skip-btn {
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
    <?php include "header_sidebar.php"; ?>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<script>toastr.error('" . $_SESSION['error'] . "', 'Error!');</script>";
        unset($_SESSION['error']);
    } elseif (isset($_SESSION["success"])) {
        echo "<script>toastr.success('" . $_SESSION['success'] . "', 'Success!');</script>";
        unset($_SESSION['success']);
    }
    ?>

    <main class="main-content">
        <section class="dashboard">
            <!-- Quick Actions Panel -->
            <div class="quick-actions mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 id="greeting" class="h3">Welcome, <span class="text-uppercase fw-bolder"><?php echo htmlspecialchars($teacherData['fullname']); ?></span>.</h2>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button class="btn btn-light btn-sm" onclick="window.location.href='post_assignment.php'">
                            <i class="bi bi-plus-circle"></i> Post Assignment
                        </button>
                        <!-- <button class="btn btn-light btn-sm" onclick="window.location.href='manage_results.php'">
                            <i class="bi bi-upload"></i> Upload Result
                        </button> -->
                        <button class="btn btn-light btn-sm" onclick="window.location.href='view_class.php'">
                            <i class="bi bi-eye"></i> View Classes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats row mb-4">
                <div class="stat-card col-md">
                    <h3><i class="bi bi-book-fill px-3"></i>Assigned Classes</h3>
                    <h1 class="fs-2"><?php echo count($assignedClasses); ?></h1>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-success" style="width: <?php echo min((count($assignedClasses) / 10) * 100, 100); ?>%;"></div>
                    </div>
                </div>
                <div class="stat-card col-md">
                    <h3><i class="bi bi-people-fill px-3"></i>Total Students</h3>
                    <h1 class="fs-2"><?php echo $totalStudents; ?></h1>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-primary" style="width: <?php echo min(($totalStudents / 200) * 100, 100); ?>%;"></div>
                    </div>
                </div>
                <div class="stat-card col-md">
                    <h3><i class="bi bi-pencil-fill px-3"></i>My Assignments</h3>
                    <h1 class="fs-2"><?php echo count($assignments); ?></h1>
                    <div class="progress mt-3">
                        <div class="progress-bar bg-warning" style="width: <?php echo min((count($assignments) / 50) * 100, 100); ?>%;"></div>
                    </div>
                </div>
            </div>



            <!-- Assignments Management -->
            <section id="assignments" class="mb-4">
                <h2>Assignments</h2>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Title</th>
                                <th>Class</th>
                                <th>Due Date</th>
                                <th>Submissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assignments)) { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No assignments posted.</td>
                                </tr>
                            <?php } else {
                                foreach ($assignments as $assignment) {
                                    $submissions = $teacher->getSubmittedAssignments($pdo, $assignment['id']);
                                    $submissionCount = count($submissions);
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['class_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                                        <td><?php echo $submissionCount; ?></td>
                                        <td>
                                            <a href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-info">View Submissions</a>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </section>



            <!-- Upload Result Form 
            <section id="upload-result" class="mb-4">
                <h2>Upload Result</h2>
                <div class="card p-3">
                    <form action="upload_result_handler.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="academic_term" class="form-label">Academic Term</label>
                            <input type="text" class="form-control" id="academic_term" name="academic_term" placeholder="e.g., 2023/2024 First Term" required>
                        </div>
                        <div class="mb-3">
                            <label for="student_admission_number" class="form-label">Student Admission Number</label>
                            <input type="text" class="form-control" id="student_admission_number" name="student_admission_number" placeholder="e.g., ADM001" required>
                        </div>
                        <div class="mb-3">
                            <label for="result_file" class="form-label">Result File (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="result_file" name="result_file" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Maximum file size: 10MB</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload Result</button>
                    </form>
                </div>
            </section>-->

            <!-- Students Section -->
            <section id="students" class="mb-4">
                <h2>Student Management</h2>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Admission No</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="students-table-body">
                            <?php
                            $allStudents = [];
                            foreach ($assignedClasses as $class) {
                                $studentsInClass = $teacher->getStudentsInClass($pdo, $class['class_name']);
                                $allStudents = array_merge($allStudents, $studentsInClass);
                            }
                            if (empty($allStudents)) {
                                echo '<tr><td colspan="4" class="text-center py-4">No students found.</td></tr>';
                            } else {
                                foreach ($allStudents as $student) {
                            ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['admission_number']); ?></td>
                                        <td><a href="view_student.php?student_id=<?php echo $student['id']; ?>" class="text-decoration-none text-primary"><?php echo htmlspecialchars($student['fullname']); ?></a></td>
                                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                                        <td>
                                            <a href="view_student.php?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">View Profile</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </main>

    <script>
        // Sidebar toggler functionality
        const header = document.querySelector('header');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        sidebar.style.height = `calc(100vh - ${header.offsetHeight}px)`;
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Optional: Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Prevent right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        function viewStudents(className) {
            // Implement view students for class
            alert('Viewing students for ' + className);
        }

        function postAssignment(className) {
            document.getElementById('post-assignment').style.display = 'block';
            document.getElementById('class_name').value = className;
            document.getElementById('post-assignment').scrollIntoView({ behavior: 'smooth' });
        }

        function viewSubmissions(assignmentId) {
            // Implement view submissions
            alert('Viewing submissions for assignment ' + assignmentId);
        }

        function showUploadResult() {
            const uploadSection = document.getElementById('upload-result');
            if (uploadSection.style.display === 'none' || uploadSection.style.display === '') {
                uploadSection.style.display = 'block';
                uploadSection.scrollIntoView({ behavior: 'smooth' });
            } else {
                uploadSection.style.display = 'none';
            }
        }

        // Load students on page load
        document.addEventListener('DOMContentLoaded', function() {
            // You can add AJAX to load students dynamically
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
                "title": "Sidebar Navigation",
                "functions": [
                    "Navigate to Dashboard, My Classes, Assignments, Results, Students, Profile, and Logout",
                    "Access different sections of the teacher portal"
                ],
                "parent": document.querySelector("#sidebar"),
                "parentLocation": document.querySelector("#sidebar").getBoundingClientRect()
            },
            {
                "title": "Welcome Greeting",
                "functions": [
                    "Personalized welcome message with teacher name",
                    "Overview of the dashboard"
                ],
                "parent": document.querySelector("#greeting"),
                "parentLocation": document.querySelector("#greeting").getBoundingClientRect()
            },
            {
                "title": "Quick Actions Panel",
                "functions": [
                    "Post new assignments, upload results, and view students",
                    "Quick access to common teacher tasks"
                ],
                "parent": document.querySelector(".quick-actions"),
                "parentLocation": document.querySelector(".quick-actions").getBoundingClientRect()
            },
            {
                "title": "Stats Cards",
                "functions": [
                    "View assigned classes, total students, and my assignments",
                    "Monitor key metrics for your teaching activities"
                ],
                "parent": document.querySelector(".stats"),
                "parentLocation": document.querySelector(".stats").getBoundingClientRect()
            },
            {
                "title": "Assigned Classes",
                "functions": [
                    "View classes assigned to you",
                    "See student counts and perform actions like viewing students or posting assignments"
                ],
                "parent": document.querySelector("#classes"),
                "parentLocation": document.querySelector("#classes").getBoundingClientRect()
            },
            {
                "title": "Assignments",
                "functions": [
                    "Manage assignments you've posted",
                    "View assignment details, due dates, submissions, and view submissions"
                ],
                "parent": document.querySelector("#assignments"),
                "parentLocation": document.querySelector("#assignments").getBoundingClientRect()
            },
            {
                "title": "Post Assignment Form",
                "functions": [
                    "Create and post new assignments",
                    "Specify title, description, subject, class, due date, and attachments"
                ],
                "parent": document.querySelector("#post-assignment"),
                "parentLocation": document.querySelector("#post-assignment").getBoundingClientRect()
            },
            {
                "title": "Upload Result Form",
                "functions": [
                    "Upload student results",
                    "Specify academic term, student admission number, and result file"
                ],
                "parent": document.querySelector("#upload-result"),
                "parentLocation": document.querySelector("#upload-result").getBoundingClientRect()
            },
            {
                "title": "Students Section",
                "functions": [
                    "Manage students across your classes",
                    "View student details and perform actions"
                ],
                "parent": document.querySelector("#students"),
                "parentLocation": document.querySelector("#students").getBoundingClientRect()
            }];
        }

        function displayCard(index) {
            let section = sections()[index];
            let card = document.createElement("div");
            card.className = "info-card";
            card.id = "info-card-" + index;

            // Show hidden sections if needed
            if (section.parent.id === 'post-assignment' || section.parent.id === 'upload-result') {
                section.parent.style.display = 'block';
            }

            // Get fresh bounding rect after showing
            let rect = section.parent.getBoundingClientRect();

            // Calculate position to avoid overflow
            let cardWidth = 300;
            let cardHeight = 200; // Approximate min height
            let top = rect.top + window.scrollY + 10;
            let left = rect.left + rect.width + 10;

            // Adjust left if it would overflow right edge
            if (left + cardWidth > window.innerWidth + window.scrollX) {
                left = rect.left - cardWidth - 10;
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
            sessionStorage.setItem('introShown', 'true');
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
                sessionStorage.setItem('introShown', 'true');
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
            sessionStorage.setItem('introShown', 'true');
        }

        // Check if intro should be shown
        document.addEventListener("DOMContentLoaded", function() {
            if (!sessionStorage.getItem('introShown')) {
                setTimeout(function() {
                    introduce();
                }, 1000); // Delay to ensure page is fully loaded
            }
        });
    </script>
</body>

</html>
