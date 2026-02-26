<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";

// Google Sheet Configuration - Using export format
// File → Share → Publish to web → Entire document → Comma-separated values (.csv)
// Add more CSV URLs as needed (each URL represents a different sheet/term)
define('GOOGLE_SHEETS_CSV_URLS', [
    'https://docs.google.com/spreadsheets/d/e/2PACX-1vS3QijuC2RMrWqFWOKR8QWGpuqDkfpYuHTdCYLcjpD0Bx04bgq5rKaldUd-QXnPcA/pub?gid=1035578848&single=true&output=csv',
    // Add more URLs here, for example:
    // 'https://docs.google.com/spreadsheets/d/e/2PACX-1vXXXXX/pub?gid=XXXXX&single=true&output=csv',
]);

/**
 * Fetch and parse Google Sheets data as CSV
 * Returns raw lines to preserve structure for custom parsing
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
        return ['headers' => [], 'data' => [], 'raw' => []];
    }
    
    // Remove BOM if present
    if (substr($csvData, 0, 3) === "\xEF\xBB\xBF") {
        $csvData = substr($csvData, 3);
    }
    
    $lines = explode("\n", trim($csvData));
    
    return ['headers' => [], 'data' => [], 'raw' => $lines];
}

/**
 * Parse the specific multi-row header format and extract student results
 * CSV Structure:
 * Row 0: Subjects,,Maths,,,,English,,,
 * Row 1: Names,,1st,2nd,Exam,Total,1st,2nd,Exam,Total
 * Row 2+: Student data like: Makinde Ayooluwa,,10,45,1065,30,30,36,42,48
 */
function getStudentResultsFromSheet($sheetUrl, $studentName) {
    $sheetData = fetchGoogleSheetData($sheetUrl);
    $rawLines = $sheetData['raw'];
    
    if (empty($rawLines)) {
        return ['headers' => [], 'results' => []];
    }
    
    // Parse header rows to get subjects and assessment types
    $subjects = [];
    $assessments = [];
    
    // First line has subjects
    $headerLine1 = str_getcsv($rawLines[0] ?? '');
    // Second line has assessment types
    $headerLine2 = str_getcsv($rawLines[1] ?? '');
    
    // Extract subjects from header line 1
    $currentSubject = '';
    for ($i = 0; $i < count($headerLine1); $i++) {
        $cell = trim($headerLine1[$i] ?? '');
        if (!empty($cell) && strtolower($cell) !== 'subjects' && strtolower($cell) !== 'names') {
            $currentSubject = $cell;
        }
        if (!empty($currentSubject) && $i >= 2) {
            $subjects[$i] = $currentSubject;
            $assessments[$i] = isset($headerLine2[$i]) ? trim($headerLine2[$i]) : '';
        }
    }
    
    // Find student by name in data rows (starting from row 2)
    $searchName = strtolower(trim($studentName));
    $results = [];
    
    for ($i = 2; $i < count($rawLines); $i++) {
        $row = str_getcsv($rawLines[$i]);
        
        if (empty($row[0])) continue;
        
        $rowName = strtolower(trim($row[0]));
        
        // Check if student name matches (partial match)
        if (strpos($rowName, $searchName) !== false || $searchName === $rowName) {
            $result = [];
            $result['name'] = trim($row[0] ?? '');
            
            // Add each assessment score from the CSV columns
            for ($col = 2; $col < count($row); $col++) {
                $subject = $subjects[$col] ?? 'Unknown';
                $assessment = $assessments[$col] ?? '';
                $key = $subject . '_' . $assessment;
                $result[$key] = trim($row[$col] ?? '');
            }
            
            $results[] = $result;
            break;
        }
    }
    
    // Build header structure for display
    $headerStructure = [];
    $headerStructure['subjects'] = array_values(array_unique($subjects));
    $headerStructure['assessments'] = array_values(array_unique(array_filter($assessments)));
    
    return ['headers' => $headerStructure, 'results' => $results];
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

// Try to fetch from Google Sheet (now supports multiple URLs)
$sheetData = ['headers' => [], 'results' => []];
if (defined('GOOGLE_SHEETS_CSV_URLS') && is_array(GOOGLE_SHEETS_CSV_URLS) && !empty(GOOGLE_SHEETS_CSV_URLS)) {
    $allResults = [];
    $allSubjects = [];
    $allAssessments = [];
    
    // Iterate through all CSV URLs and fetch results
    foreach (GOOGLE_SHEETS_CSV_URLS as $csvUrl) {
        if (!empty($csvUrl)) {
            $result = getStudentResultsFromSheet($csvUrl, $studentFullName);
            
            // Merge results from each sheet
            if (!empty($result['results'])) {
                $allResults = array_merge($allResults, $result['results']);
            }
            
            // Merge subjects and assessments
            if (!empty($result['headers']['subjects'])) {
                $allSubjects = array_merge($allSubjects, $result['headers']['subjects']);
            }
            if (!empty($result['headers']['assessments'])) {
                $allAssessments = array_merge($allAssessments, $result['headers']['assessments']);
            }
        }
    }
    
    // Build final sheet data structure
    $sheetData = [
        'headers' => [
            'subjects' => array_values(array_unique($allSubjects)),
            'assessments' => array_values(array_unique(array_filter($allAssessments)))
        ],
        'results' => $allResults
    ];
}

// Also fetch from database as fallback
$dbResults = fetchResultsFromDB($pdo, $studentData);

// Determine what to display
$hasGoogleSheetData = !empty($sheetData['results']);
$hasDbResults = !empty($dbResults);
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0"><i class="bi bi-file-earmark-text me-2"></i>My Academic Results</h1>
                    <small class="text-muted">View all your uploaded results</small>
                </div>

                <?php if (!$hasGoogleSheetData && !$hasDbResults): ?>
                    <div class="no-results">
                        <i class="bi bi-file-earmark-x"></i>
                        <h4>No Results Found</h4>
                        <p>Your academic results will appear here once they are uploaded by your administrators.</p>
                    </div>
                <?php else: ?>
                    
                    <!-- Display Google Sheet Results (Table Format matching CSV structure) -->
                    <?php if ($hasGoogleSheetData): ?>
                        <?php 
                        $subjects = $sheetData['headers']['subjects'] ?? [];
                        $assessments = $sheetData['headers']['assessments'] ?? [];
                        $studentResult = $sheetData['results'][0] ?? [];
                        ?>
                        <div class="result-card mb-4">
                            <div class="result-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-table me-2"></i>
                                    Academic Results
                                </h5>
                                <small>Student: <?php echo htmlspecialchars($studentFullName); ?></small>
                            </div>
                            <div class="">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <?php foreach ($assessments as $assessment): ?>
                                                    <th><?php echo htmlspecialchars($assessment); ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subjects as $subject): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($subject); ?></strong></td>
                                                    <?php foreach ($assessments as $assessment): 
                                                        $key = $subject . '_' . $assessment;
                                                        $value = $studentResult[$key] ?? '-';
                                                    ?>
                                                        <td><?php echo htmlspecialchars($value); ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Display Database Results (Iframe Format) -->
                    <?php if ($hasDbResults): ?>
                        <div class="row">
                            <?php foreach ($dbResults as $result): ?>
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
