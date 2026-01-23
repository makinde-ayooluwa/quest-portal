<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Upload Result | Quest Portal</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .upload-card {
            max-width: 800px;
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

        .student-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }

        .student-card:hover {
            border-color: #dee2e6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .student-card.selected {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .student-search-container {
            position: relative;
        }

        .student-results {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background: #fff;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 1rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
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

    <div class="upload-card">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Student Result</h2>
            <small class="text-muted">Select student, enter term, and provide result URL</small>
        </div>

        <form id="uploadResultForm" action="result_upload_handler.php" method="post">
            <h5 class="mb-3"><i class="bi bi-person-check me-2"></i>Student Selection</h5>
            <div class="mb-4">
                <label class="form-label">Search and Select Student <span class="text-danger">*</span></label>
                <input type="hidden" id="student_admission_number" name="student_admission">
                <div class="student-search-container">
                    <input type="text" id="studentSearchInput" placeholder="Type student name to search..." class="form-control mb-2">
                    <div class="loading" id="loadingIndicator">
                        <i class="bi bi-arrow-repeat spin me-2"></i>Loading students...
                    </div>
                    <div class="student-results d-none" id="studentResults"></div>
                </div>
                <div class="form-text">Click on a student card to select them.</div>
            </div>

            <h5 class="mb-3"><i class="bi bi-calendar-event me-2"></i>Academic Details</h5>
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Academic Term <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="academic_term" placeholder="e.g. First Term 2024/2025" required>
                    <div class="form-text">Enter the academic term for this result.</div>
                </div>
            </div>

            <h5 class="mb-3"><i class="bi bi-link-45deg me-2"></i>Result Document</h5>
            <div class="mb-4">
                <label class="form-label">Result URL <span class="text-danger">*</span></label>
                <input type="url" class="form-control" name="result_file" id="result_url"
                       placeholder="https://docs.google.com/spreadsheets/d/..." required>
                <div class="form-text">Provide a link to the student's result document (Google Sheets, PDF, etc.).</div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="./manage_results.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-grad"><i class="bi bi-upload me-1"></i> Upload Result</button>
            </div>
        </form>
    </div>

    <script>
        let studentsData = [];
        const studentSearchInput = document.getElementById('studentSearchInput');
        const studentResults = document.getElementById('studentResults');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const studentAdmissionInput = document.getElementById('student_admission_number');

        // Load students data
        function loadStudents() {
            loadingIndicator.classList.remove('d-none');
            fetch("ajax_data_for_students.php")
                .then(res => res.json())
                .then(data => {
                    studentsData = data;
                    loadingIndicator.classList.add('d-none');
                    renderStudents(data);
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    loadingIndicator.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Error loading students';
                });
        }

        // Render student cards
        function renderStudents(students) {
            studentResults.innerHTML = '';
            if (students.length === 0) {
                studentResults.innerHTML = '<div class="p-3 text-center text-muted">No students found</div>';
                studentResults.classList.remove('d-none');
                return;
            }

            students.forEach(student => {
                const card = document.createElement('div');
                card.className = 'student-card p-3 mb-2 rounded';
                card.id = student.admission_number;
                card.onclick = () => selectStudent(card);

                card.innerHTML = `
                    <div class="d-flex align-items-center">
                        <img class="rounded-circle me-3" width="50" height="50" src="../${student.picture}" alt="Profile">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${student.fullname}</div>
                            <small class="text-muted">Class: ${student.class.toUpperCase()} | Adm. #: ${student.admission_number}</small>
                        </div>
                        <i class="bi bi-check-circle-fill text-success d-none" id="check-${student.admission_number}"></i>
                    </div>
                `;

                studentResults.appendChild(card);
            });

            studentResults.classList.remove('d-none');
        }

        // Select student
        function selectStudent(card) {
            const admissionNumber = card.id;
            studentAdmissionInput.value = admissionNumber;

            // Update visual selection
            document.querySelectorAll('.student-card').forEach(c => {
                c.classList.remove('selected');
                c.querySelector('i[id^="check-"]').classList.add('d-none');
            });

            card.classList.add('selected');
            card.querySelector(`#check-${admissionNumber}`).classList.remove('d-none');

            // Hide results after selection
            setTimeout(() => {
                studentResults.classList.add('d-none');
            }, 500);
        }

        // Search functionality
        studentSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            if (query.length === 0) {
                studentResults.classList.add('d-none');
                return;
            }

            const filteredStudents = studentsData.filter(student =>
                student.fullname.toLowerCase().includes(query) ||
                student.admission_number.toLowerCase().includes(query) ||
                student.class.toLowerCase().includes(query)
            );

            renderStudents(filteredStudents);
        });

        // Load students on page load
        loadStudents();

        // Form validation
        document.getElementById('uploadResultForm').addEventListener('submit', function(e) {
            const studentSelected = studentAdmissionInput.value.trim();
            const academicTerm = this.querySelector('input[name="academic_term"]').value.trim();
            const resultUrl = this.querySelector('input[name="result_file"]').value.trim();

            if (!studentSelected) {
                e.preventDefault();
                toastr.error('Please select a student.', 'Validation Error');
                return false;
            }

            if (!academicTerm) {
                e.preventDefault();
                toastr.error('Please enter the academic term.', 'Validation Error');
                return false;
            }

            if (!resultUrl) {
                e.preventDefault();
                toastr.error('Please provide a result URL.', 'Validation Error');
                return false;
            }

            // Basic URL validation
            try {
                new URL(resultUrl);
            } catch {
                e.preventDefault();
                toastr.error('Please enter a valid URL.', 'Validation Error');
                return false;
            }

            return true;
        });
    </script>

    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>
