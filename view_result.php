<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Google Sheet Configuration - Using your spreadsheet ID
// File → Share → Publish to web → Entire document → Comma-separated values (.csv)
define('GOOGLE_SHEETS_CSV_URL', 'https://docs.google.com/spreadsheets/d/e/17vy-_nifUOAGizuX_OdwlcKrjdZfBL0xO_eBhQ_JO6o/Primary One/pub?output=csv');

/**
 * Fetch and parse Google Sheets data as CSV
 */
function fetchGoogleSheetData($sheetUrl) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);
    
    $csvData = @file_get_contents($sheetUrl, false, $context);
    
    if ($csvData === false) {
        return [];
    }
    
    $lines = explode("\n", trim($csvData));
    $data = [];
    
    foreach ($lines as $lineIndex => $line) {
        if ($lineIndex === 0) continue; // Skip header row
        $row = str_getcsv($line);
        if (!empty($row[0])) {
            $data[] = $row;
        }
    }
    
    return $data;
}

/**
 * Get results for a specific student by name
 */
function getStudentResultsFromSheet($sheetUrl, $studentName) {
    $allData = fetchGoogleSheetData($sheetUrl);
    $results = [];
    
    // Normalize student name for comparison
    $searchName = strtolower(trim($studentName));
    
    foreach ($allData as $row) {
        // Assuming column 0 is the student name - adjust index as needed
        $rowName = strtolower(trim($row[0] ?? ''));
        
        // Check if student name matches (partial match)
        if (strpos($rowName, $searchName) !== false || $searchName === $rowName) {
            $results[] = [
                'name' => $row[0] ?? '',
                'term' => $row[1] ?? '',
                'subject' => $row[2] ?? '',
                'score' => $row[3] ?? '',
                'grade' => $row[4] ?? '',
                'remarks' => $row[5] ?? '',
                'teacher' => $row[6] ?? ''
            ];
        }
    }
    
    return $results;
}

// Fetch results from database (original method)
function fetchResultsFromDB($pdo, $studentData)
{
    $sql = "SELECT * FROM results WHERE student_admission_number = :admission_number ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":admission_number", $studentData["admission_number"]);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get student's full name from database
$studentFullName = $studentData['fullname'] ?? '';

// Try to fetch from Google Sheet (if URL is configured)
$googleSheetResults = [];
if (defined('GOOGLE_SHEETS_CSV_URL') && GOOGLE_SHEETS_CSV_URL !== '' && strpos(GOOGLE_SHEETS_CSV_URL, 'YOUR_PUBLISHED_SHEET_ID') === false) {
    $googleSheetResults = getStudentResultsFromSheet(GOOGLE_SHEETS_CSV_URL, $studentFullName);
}

// Also fetch from database as fallback
$dbResults = fetchResultsFromDB($pdo, $studentData);

// Combine results (Google Sheet takes priority)
$studentResult = $dbResults;
$hasGoogleSheetData = !empty($googleSheetResults);
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
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
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

        .result-iframe {
            width: 100%;
            height: 600px;
            border: none;
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

    <div class="container-fluid px-2 px-md-4 py-4 main-content">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text me-2"></i>My Academic Results</h1>
                    <small class="text-muted">View all your uploaded results</small>
                </div>

                <?php if (empty($studentResult) && empty($googleSheetResults)): ?>
                    <div class="no-results">
                        <i class="bi bi-file-earmark-x"></i>
                        <h4>No Results Found</h4>
                        <p>Your academic results will appear here once they are uploaded by your administrators.</p>
                    </div>
                <?php else: ?>
                    <!-- Display Google Sheet Results (Table Format) -->
                    <?php if ($hasGoogleSheetData): ?>
                        <div class="result-card mb-4">
                            <div class="result-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-table me-2"></i>
                                    Academic Results from Google Sheets
                                </h5>
                                <small>Student: <?php echo htmlspecialchars($studentFullName); ?></small>
                            </div>
                            <div class="p-3">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Term</th>
                                                <th>Subject</th>
                                                <th>Score</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                                <th>Teacher</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($googleSheetResults as $sheetResult): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($sheetResult['term']); ?></td>
                                                    <td><?php echo htmlspecialchars($sheetResult['subject']); ?></td>
                                                    <td><?php echo htmlspecialchars($sheetResult['score']); ?></td>
                                                    <td><?php echo htmlspecialchars($sheetResult['grade']); ?></td>
                                                    <td><?php echo htmlspecialchars($sheetResult['remarks']); ?></td>
                                                    <td><?php echo htmlspecialchars($sheetResult['teacher']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Display Database Results (Iframe Format) -->
                    <?php if (!empty($studentResult)): ?>
                        <div class="row">
                            <?php foreach ($studentResult as $result): ?>
                                <div class="col-12">
                                    <div class="result-card">
                                        <div class="result-header">
                                            <h5 class="mb-0">
                                                <i class="bi bi-calendar-event me-2"></i>
                                                <?php echo htmlspecialchars($result["academic_term"]); ?>
                                            </h5>
                                            <small>Uploaded on <?php echo date('M j, Y', strtotime($result["uploaded_at"] ?? 'now')); ?></small>
                                        </div>
                                        <div class="p-0">
                                            <iframe class="result-iframe" src="<?php echo htmlspecialchars($result["result_file"]); ?>" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
</body>

</html>