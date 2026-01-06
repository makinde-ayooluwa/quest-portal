<!DOCTYPE html>
<html lang="en">

<head>
    <title>Academic Records Upload - Quest Schools Admin</title>
    <?php include "head.php" ?>
    <style>
        * {
            font-family: Montserrat;
        }

        body {
            background: #f8f9fa;
        }

        .upload-card {
            max-width: 500px;
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
    <div class="upload-card">
        <h2 class="mb-4 text-center"><i class="bi bi-cloud-upload me-2"></i>Academic Records Upload</h2>
        <form action="academic_records_upload.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="studentName" class="form-label">Student Name</label>
                <input type="text" class="form-control" id="studentName" name="studentName" required>
            </div>
            <div class="mb-3">
                <label for="rollNo" class="form-label">Roll Number</label>
                <input type="text" class="form-control" id="rollNo" name="rollNo" required>
            </div>
            <div class="mb-3">
                <label for="class" class="form-label">Class</label>
                <input type="text" class="form-control" id="class" name="class" required>
            </div>
            <div class="mb-3">
                <label for="term" class="form-label">Term</label>
                <select class="form-select" id="term" name="term" required>
                    <option value="">Select Term</option>
                    <option>First Term</option>
                    <option>Second Term</option>
                    <option>Third Term</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="recordFile" class="form-label">Upload Academic Record</label>
                <input class="form-control" type="file" id="recordFile" name="recordFile" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                <div class="form-text">Accepted formats: PDF, DOC, DOCX, XLS, XLSX</div>
            </div>
            <div class="mb-3">
                <label for="comments" class="form-label">Comments (optional)</label>
                <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-grad"><i class="bi bi-upload me-1"></i>Upload Record</button>
                <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>
        </form>
    </div>
    <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>