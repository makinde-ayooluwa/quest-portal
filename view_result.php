<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
$results = $student->getResults($pdo, $studentData["admission_number"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Results | Quest Portal</title>
    <?php include "head.php"; ?>
    <style>
        * {
            font-family: Montserrat;
        }

        html {
            overflow-x: hidden;
        }

        body {
            background: #f8f9fa;
        }

        .result-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            margin-bottom: 1.5rem;
            overflow-x: scroll;
            scrollbar-width: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100vh;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .result-header {
            background: linear-gradient(90deg, #0d6efd 0%, #198754 100%);
            color: #fff;
            padding: 1rem;
        }

        .iframe-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .result-iframe {
            scrollbar-width: none;
            flex: 1;
            width: inherit;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .main-content {
            margin-left: 0;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 177.5px;
            }
        }

        @media (min-width: 1205px) {
            .main-content {
                margin-left: 250px;
            }
        }

        @media (max-width: 991px) {
            .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .result-iframe {
                height: 400px;
            }
        }

        @media (max-width: 576px) {
            .result-iframe {
                height: 300px;
            }

            .result-header {
                padding: 0.75rem;
            }

            .result-header h5 {
                font-size: 1rem;
            }

            .result-header small {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <?php include "header.php"; ?>
    <?php include "sidebar.php"; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text me-2"></i>My Academic Results</h1>
                    <small class="text-muted">View all your uploaded results</small>
                </div>
                <?php
                if (count($results) < 1) {
                ?>
                    <div class="no-results">
                        <i class="bi bi-file-earmark-x"></i>
                        <h4>No Results Found</h4>
                        <p>Your academic results will appear here once they are uploaded by your administrators.</p>
                    </div>
                    <?php
                } else {
                    foreach ($results as $result) {
                    ?>
                        <div class="result-card mb-4">
                            <div class="result-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-table me-2"></i>
                                    Academic Results
                                </h5>
                                <small>Student: <?php echo htmlspecialchars($studentData['fullname']); ?></small>
                            </div>
                            <div class="iframe-container">
                                <iframe src="<?php echo $result['result_file'] ?>" class="result-iframe" style="height: 100%;" frameborder="0"></iframe>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>

            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>