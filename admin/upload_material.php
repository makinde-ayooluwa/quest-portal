<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle upload securely
    $materialTitle = trim($_POST["materialTitle"] ?? '');
    $materialType = trim($_POST["materialType"] ?? '');
    $uploadedBy = $adminData["fullname"];

    if (empty($materialTitle) || empty($materialType) || !isset($_FILES["materialFile"])) {
        $_SESSION["upload_error"] = "All fields are required.";
        header('Location: upload_material.php');
        exit();
    }

    $file = $_FILES['materialFile'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['upload_error'] = 'File upload error.';
        header('Location: upload_material.php');
        exit();
    }

    // Validate file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        $_SESSION['upload_error'] = 'File too large (max 10MB).';
        header('Location: upload_material.php');
        exit();
    }

    // Validate extension and mime
    $allowedExt = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        $_SESSION['upload_error'] = 'File type not allowed.';
        header('Location: upload_material.php');
        exit();
    }

    $uploadDir = 'assets/uploads/materials/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newName = 'material_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $destPath = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        $_SESSION['upload_error'] = 'Failed to move uploaded file.';
        header('Location: upload_material.php');
        exit();
    }

    // Store relative path
    $relativePath = 'assets/uploads/materials/' . $newName;

    try {
        $q = "INSERT INTO materials (materialTitle, materialType, materialFile, uploaded_by) VALUES (:title, :type, :file, :uploaded_by)";
        $s = $pdo->prepare($q);
        $s->bindParam(':title', $materialTitle);
        $s->bindParam(':type', $materialType);
        $s->bindParam(':file', $relativePath);
        $s->bindParam(':uploaded_by', $uploadedBy);
        $s->execute();
        $_SESSION['upload_success'] = 'Material uploaded successfully.';
    } catch (PDOException $e) {
        error_log('Material upload DB error: ' . $e->getMessage());
        $_SESSION['upload_error'] = 'Database error while saving material.';
    }

    header('Location: upload_material.php');
    exit();
} else {
    // GET: render upload form and list existing materials
    $materials = [];
    try {
        $stmt = $pdo->query("SELECT * FROM materials ORDER BY uploaded_on DESC LIMIT 100");
        $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Fetch materials error: ' . $e->getMessage());
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php include "head.php"; ?>
        <title>Upload Material - Admin</title>
        <style>
            .upload-card {
                max-width: 900px;
                margin: 2.5rem auto;
                padding: 1.25rem;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06)
            }
        </style>
    </head>

    <body>
    <?php include "settings.php" ?>
        <?php include 'header_sidebar.php'; ?>

        <div class="container-fluid">
            <div class="upload-card">
                <h4 class="mb-3">Upload Material</h4>
                <?php if (isset($_SESSION['upload_error'])) { ?>
                    <script>
                        toastr.error("<?php echo htmlspecialchars($_SESSION['upload_error'], ENT_QUOTES, 'UTF-8') ?>", "Error!");
                    </script>
                <?php unset($_SESSION['upload_error']);
                } ?>
                <?php if (isset($_SESSION['upload_success'])) { ?>
                    <script>
                        toastr.success("<?php echo htmlspecialchars($_SESSION['upload_success'], ENT_QUOTES, 'UTF-8') ?>", "Success!");
                    </script>
                <?php unset($_SESSION['upload_success']);
                } ?>

                <form action="upload_material.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="materialTitle" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select name="materialType" class="form-select">
                                <option>Document</option>
                                <option>Image</option>
                                <option>Slide</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" name="materialFile" class="form-control" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Upload</button>
                </form>

                <hr>
                <h5 class="mt-3">Recently uploaded</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>File</th>
                                <th>Uploaded by</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($materials)) { ?>
                                <tr>
                                    <td colspan="5" class="text-muted">No materials uploaded yet.</td>
                                </tr>
                                <?php } else {
                                foreach ($materials as $m) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($m['materialTitle']); ?></td>
                                        <td><?php echo htmlspecialchars($m['materialType']); ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-2" href="<?php echo htmlspecialchars( $m['materialFile']); ?>" download>
                                                <i class="bi bi-download" aria-hidden="true"></i>
                                                <span class="visually-hidden">Download <?php echo htmlspecialchars($m['materialTitle']); ?></span>
                                                <span class="d-none d-md-inline">Download</span>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($m['uploaded_by']); ?><span class="badge bg-primary"><?php echo $adminData["staff_role"] ?></span></td>
                                        <td><?php echo htmlspecialchars($m['uploaded_on']); ?></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/jquery.min.js"></script>
    </body>

    </html>
<?php
}
