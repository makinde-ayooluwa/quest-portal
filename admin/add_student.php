<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Student - Quest Schools Admin</title>
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

    <div class="add-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New Student</h2>
            <small class="text-muted">Fill required fields and click Add Student</small>
        </div>
        <form id="addStudentForm" action="add_student_handler.php" method="post" enctype="multipart/form-data">
            <h5 class="mb-3">Personal Details</h5>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="fullname" required placeholder="e.g. John Doe" autofocus>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required placeholder="name@example.com">
                    <div class="form-text">Primary contact / login email for the student account.</div>
                </div>
            </div>
            <h5 class="mb-3">Admission Details</h5>
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Admission Number / ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="admission_number" required placeholder="e.g. 25/QC0001">
                    <div class="form-text">Unique admission number used for records.</div>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class" id="class" class="form-select" required>
                        <option value="">Select Class</option>
                        <?php
                        $classes = $admin->getClasses($pdo);
                        if (!empty($classes)) {
                            foreach ($classes as $class) {
                        ?>
                                <option value="<?php echo htmlspecialchars($class['class_name']) ?>"><?php echo htmlspecialchars($class['class_name']) ?></option>
                        <?php
                            }
                        } else {
                            echo '<option value="">No classes available</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="./" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Add Student</button>
            </div>
        </form>
        <div class="add-options">
            <div class="container">
                <h1 class="pt-3 h6 text-primary">Add students dynamically with an excel file</h1>
                <input class="form-control" type="file" accept=".xlsx" id="excel_file_input" placeholder="Choose an excel file">
                <button class="btn btn-success" id="upload_btn">Add Students</button>
            </div>
        </div>
        <script>
            let selectedFile = null;

            // Listen for file selection
            document.getElementById('excel_file_input').addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files[0]) {
                    selectedFile = files[0]; // Store the selected file
                    console.log('File selected:', selectedFile.name);
                }
            }, false);

            // Listen for button click to process and send the file
            document.getElementById('upload_btn').addEventListener('click', function() {
                if (!selectedFile) {
                    alert('Please select an Excel file first.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });

                    // Get the first worksheet
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];

                    // Convert worksheet to JSON objects
                    const jsonData = XLSX.utils.sheet_to_json(worksheet);

                    // Optional: display in console
                    console.log(jsonData);

                    // Convert JSON to string for sending
                    const jsonString = JSON.stringify(jsonData);

                    // Create FormData and append the JSON string
                    const formData = new FormData();
                    formData.append('excel_data', jsonString);

                    // Send to PHP backend
                    fetch('add_student_in_bulk.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log('Server response:', data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                };

                reader.readAsArrayBuffer(selectedFile); // Read the Excel file
            });
        </script>
    </div>
    <script>
        // client-side quick validation to improve UX
        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value.trim();
            const adm = this.querySelector('input[name="admission_number"]').value.trim();
            const cls = this.querySelector('select[name="class"]').value;
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(email)) {
                e.preventDefault();
                toastr.error('Please enter a valid email address.');
                return false;
            }
            if (!adm) {
                e.preventDefault();
                toastr.error('Admission number is required.');
                return false;
            }
            if (!cls) {
                e.preventDefault();
                toastr.error('Please select a class.');
                return false;
            }
            return true;
        });
    </script>
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