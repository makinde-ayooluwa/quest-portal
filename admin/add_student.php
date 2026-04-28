<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
$classes = $admin->getClasses($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Student - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        .main-content {
            margin-left: 220px; padding: 2rem 1rem;
        }
        .add-card {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            animation: fadeInUp 0.5s ease forwards;
            overflow: hidden;
            position: relative;
        }
        .add-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
        }
        .section-title-admin {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.125rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            color: var(--slate-800);
        }
        .section-title-admin::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--slate-200), transparent);
            margin-left: 0.5rem;
        }
        .btn-save {
            background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
            color: #fff; border: none;
            transition: all var(--transition-base);
            position: relative; overflow: hidden;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow-green);
            color: #fff;
        }
        .excel-upload {
            background: linear-gradient(135deg, var(--quest-green-50), var(--slate-50));
            border: 2px dashed var(--quest-green-200);
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
            transition: all var(--transition-base);
        }
        .excel-upload:hover {
            border-color: var(--quest-green-400);
            background: linear-gradient(135deg, var(--quest-green-100), var(--slate-100));
        }
        @media (max-width: 1024px) {
            .main-content { margin-left: 0; }
        }
    </style>
</head>

<body>
    <?php include "settings.php" ?>
    <?php include "header_sidebar.php" ?>
    <?php if (isset($_SESSION['error'])) { ?>
        <script>toastr.error("<?php echo htmlspecialchars($_SESSION["error"], ENT_QUOTES, 'UTF-8') ?>", "Error!");</script>
    <?php unset($_SESSION['error']);
    } else if (isset($_SESSION['success'])) { ?>
        <script>toastr.success("<?php echo htmlspecialchars($_SESSION["success"], ENT_QUOTES, 'UTF-8') ?>", "Success!");</script>
    <?php unset($_SESSION['success']);
    } ?>

    <div class="container-fluid main-content">
        <div class="add-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1 fw-bold"><i class="bi bi-person-plus text-green me-2"></i>Add New Student</h2>
                    <small class="text-muted">Fill required fields and click Add Student</small>
                </div>

            <form id="addStudentForm" action="add_student_handler.php" method="post" enctype="multipart/form-data">
                <div class="section-title-admin"><i class="bi bi-person text-green"></i>Personal Details</div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="fullname" required placeholder="e.g. John Doe" autofocus>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required placeholder="name@example.com">
                        <div class="form-text text-muted">Primary contact / login email for the student account.</div>
                </div>

                <div class="section-title-admin"><i class="bi bi-building text-green"></i>Admission Details</div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Admission Number / ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="admission_number" required placeholder="e.g. 25/QC0001">
                        <div class="form-text text-muted">Unique admission number used for records.</div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                        <select name="class" id="class" class="form-select" required>
                            <option value="">Select Class</option>
                            <?php
                            if (!empty($classes)) {
                                foreach ($classes as $class) {
                            ?>
                                    <option value="<?php echo htmlspecialchars($class['class_name']) ?>"><?php echo htmlspecialchars($class['class_name']) ?></option>
                            <?php }
                            } else {
                                echo '<option value="">No classes available</option>';
                            }
                            ?>
                        </select>
                    </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="./" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Cancel</a>
                    <button type="submit" class="btn btn-save"><i class="bi bi-person-plus me-2"></i>Add Student</button>
                </div>
            </form>

            <div class="excel-upload">
                <i class="bi bi-file-earmark-excel fs-2 text-green mb-2 d-block"></i>
                <h5 class="fw-bold mb-2">Bulk Upload via Excel</h5>
                <p class="text-muted mb-3">Import multiple students at once using an Excel file.</p>
                <input class="form-control mb-3" type="file" accept=".xlsx" id="excel_file_input">
                <button class="btn btn-success" id="upload_btn"><i class="bi bi-upload me-2"></i>Add Students</button>
            </div>
    </div>

    <script>
        let selectedFile = null;
        document.getElementById('excel_file_input').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files[0]) {
                selectedFile = files[0];
                console.log('File selected:', selectedFile.name);
            }
        }, false);

        document.getElementById('upload_btn').addEventListener('click', function() {
            if (!selectedFile) {
                alert('Please select an Excel file first.');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                const jsonString = JSON.stringify(jsonData);
                const formData = new FormData();
                formData.append('excel_data', jsonString);
                fetch('add_student_in_bulk.php', { method: 'POST', body: formData })
                    .then(response => response.text())
                    .then(data => { console.log('Server response:', data); })
                    .catch(error => { console.error('Error:', error); });
            };
            reader.readAsArrayBuffer(selectedFile);
        });

        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value.trim();
            const adm = this.querySelector('input[name="admission_number"]').value.trim();
            const cls = this.querySelector('select[name="class"]').value;
            const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(email)) { e.preventDefault(); toastr.error('Please enter a valid email address.'); return false; }
            if (!adm) { e.preventDefault(); toastr.error('Admission number is required.'); return false; }
            if (!cls) { e.preventDefault(); toastr.error('Please select a class.'); return false; }
            return true;
        });
    </script>
    <script>
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
    </script>
</body>

</html>
