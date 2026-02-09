<?php
session_start();
require_once 'student_includes/autoloader.inc.php';
require_once 'student_includes/db.inc.php';

include "student_includes/student.inc.php";
function fetchResults($pdo, $studentData)
{
  $sql = "SELECT * FROM results WHERE student_admission_number = :admission_number";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(":admission_number", $studentData["admission_number"]);
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$studentResult = fetchResults($pdo, $studentData);



?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Student Result - QUEST STUDENT</title>
  <?php include "head.php" ?>
  <style>
    * {
      font-family: Montserrat;
    }

    body {
      background: #f8f9fa;
    }

    .result-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin: 2rem auto;
      max-width: 700px;
    }

    .result-header {
      border-bottom: 1px solid #eee;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
    }

    .result-table th,
    .result-table td {
      vertical-align: middle;
    }

    .progress {
      height: 7px;
    }

    .back-link {
      text-decoration: none;
      color: #0d6efd;
      font-weight: 500;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    :root {
      --quest-yellow: #fec511;
      --quest-green: #5aac7b;
    }

    .text-green {
      color: var(--quest-green);
    }

    .text-yellow {
      color: var(--quest-yellow);
    }

    .bg-grad {
      background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
    }

    .btn-grad {
      background: linear-gradient(90deg, var(--quest-green), var(--quest-yellow));
    }

    .btn-grad:hover {
      background: linear-gradient(90deg, var(--quest-yellow), var(--quest-green));
    }

    .bg-yellow {
      background: var(--quest-yellow);
    }

    .bg-green {
      background: var(--quest-green);
    }

    .form-container {
      width: 482px;
      border-radius: 12px;
      margin-top: 20px;
    }

    .toggler {
      display: grid;
    }

    .toggler span {
      margin: 5px 5px;
      border-radius: 20px;
      padding: 2px 30px;
      background: #767676;
    }

    .toggler-parent {
      margin-top: 10px;
    }

    .sidebar {
      background: #fff;
      z-index: 20;
      position: fixed;
    }

    [closed-sidebar] {
      overflow: hidden;
      left: -100%;
    }

    .side-links a {
      text-decoration: none;
      color: black;
      font-weight: bolder;
      margin-bottom: 5px;
      border-radius: 5px;
      text-align: center;
      padding: 10px;
      transition: background 0.3s ease-in-out;
    }

    .side-links a:hover {
      background: rgba(115, 115, 115, 0.1);
    }

    @media(min-width:992px) {
      .toggler-parent {
        display: none;
      }

      .sidebar {
        left: 0%;
        position: fixed;
      }
    }

    * {
      font-family: Montserrat;
    }
  </style>
</head>

<body>
  <?php include "header.php" ?>
  <?php include "sidebar.php" ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-lg-9">
        <div class="container">
          <div class="result-card">
            <div class="result-header d-flex justify-content-between align-items-center">
              <div>
                <h2 class="mb-0">Student Result</h2>
                <p class="mb-0 text-muted">Name: <strong><?php echo $studentData["fullname"] ?></strong></p>
                <p class="mb-0 text-muted">Class: <strong><?php echo $studentData["class"] ?></strong></p>
              </div>
              <img src="<?php echo $studentData["picture"] ?>" alt="Student Avatar" width="60"
                class="rounded-circle border">
            </div>
            <div class="mb-4">
              <h4 class="mb-3">Academic Results</h4>
              <div class="table-responsive" style="overflow-x: scroll;">
                <table class="table table-bordered result-table">
                  <thead class="table-dark">
                    <tr>
                      <th>Term</th>
                      <th>Date Uploaded</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (empty($studentResult)) {
                      ?>
                      <tr>
                        <td colspan="3" class="text-center py-4">
                          <i class="bi bi-file-earmark-x fs-2 text-muted mb-2"></i>
                          <div>No results yet. Check back later</div>
                        </td>
                      </tr>
                      <?php
                    }
                    foreach ($studentResult as $result) {
                      ?>
                      <tr>
                        <td class="fw-bold">
                          <?php echo htmlspecialchars($result["academic_term"]) ?>
                        </td>
                        <td>
                          <?php echo date('M j, Y', strtotime($result["added_on"])) ?>
                        </td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
              <div class="container d-flex justify-content-end">
                <a href="view_result.php" class="btn btn-primary m-3 p-2"><i class="bi bi-eye me-3"></i> View All</a>
              </div>
            </div>

            
            <!-- <div class="mb-4">
              <h4 class="mb-3">Assignment Performance</h4>
              <?php
              // Fetch assignment performance data
              $performanceQuery = "SELECT
                                    COUNT(DISTINCT a.id) as total_assignments,
                                    COUNT(CASE WHEN asub.status = 'graded' AND asub.student_id = :student_id THEN 1 END) as graded_assignments,
                                    AVG(CASE WHEN asub.grade REGEXP '^[0-9]+$' AND asub.student_id = :student_id THEN CAST(asub.grade AS DECIMAL(5,2)) END) as avg_grade,
                                    COUNT(CASE WHEN asub.status = 'late' AND asub.student_id = :student_id THEN 1 END) as late_submissions
                                   FROM assignments a
                                   LEFT JOIN assignment_submissions asub ON a.id = asub.assignment_id
                                   WHERE a.class_name = :class_name";
              $performanceStmt = $pdo->prepare($performanceQuery);
              $performanceStmt->bindParam(":student_id", $studentData['id']);
              $performanceStmt->bindParam(":class_name", $studentData['class']);
              $performanceStmt->execute();
              $performance = $performanceStmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <div class="row g-3">
                <div class="col-md-3">
                  <div class="card border-primary">
                    <div class="card-body text-center">
                      <i class="bi bi-journal-check fs-2 text-primary mb-2"></i>
                      <h5 class="card-title"><?php echo $performance['total_assignments'] ?? 0; ?></h5>
                      <p class="card-text small text-muted">Total Assignments</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card border-success">
                    <div class="card-body text-center">
                      <i class="bi bi-check-circle fs-2 text-success mb-2"></i>
                      <h5 class="card-title"><?php echo $performance['graded_assignments'] ?? 0; ?></h5>
                      <p class="card-text small text-muted">Graded</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card border-info">
                    <div class="card-body text-center">
                      <i class="bi bi-graph-up fs-2 text-info mb-2"></i>
                      <h5 class="card-title">
                        <?php echo $performance['avg_grade'] ? number_format($performance['avg_grade'], 1) : 'N/A'; ?>
                      </h5>
                      <p class="card-text small text-muted">Average Grade</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card border-warning">
                    <div class="card-body text-center">
                      <i class="bi bi-clock fs-2 text-warning mb-2"></i>
                      <h5 class="card-title"><?php echo $performance['late_submissions'] ?? 0; ?></h5>
                      <p class="card-text small text-muted">Late Submissions</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            
            <div>
              <h4 class="mb-3">Recent Submissions</h4>
              <?php
              $recentQuery = "SELECT a.title, a.subject, asub.submitted_at, asub.grade, asub.status, asub.feedback
                             FROM assignment_submissions asub
                             JOIN assignments a ON asub.assignment_id = a.id
                             WHERE asub.student_id = :student_id
                             ORDER BY asub.submitted_at DESC LIMIT 5";
              $recentStmt = $pdo->prepare($recentQuery);
              $recentStmt->bindParam(":student_id", $studentData['id']);
              $recentStmt->execute();
              $recentSubmissions = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
              ?>

              <?php if (empty($recentSubmissions)): ?>
                <div class="text-center py-4">
                  <i class="bi bi-upload fs-2 text-muted mb-2"></i>
                  <div class="text-muted">No submissions yet</div>
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>Assignment</th>
                        <th>Subject</th>
                        <th>Submitted</th>
                        <th>Grade</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($recentSubmissions as $submission): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($submission['title']); ?></td>
                          <td><?php echo htmlspecialchars($submission['subject']); ?></td>
                          <td><?php echo date('M j, Y', strtotime($submission['submitted_at'])); ?></td>
                          <td>
                            <?php if ($submission['grade']): ?>
                              <span class="badge bg-success"><?php echo htmlspecialchars($submission['grade']); ?></span>
                            <?php else: ?>
                              <span class="text-muted">-</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <span
                              class="badge bg-<?php echo $submission['status'] === 'graded' ? 'primary' : 'secondary'; ?>">
                              <?php echo ucfirst($submission['status']); ?>
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div> -->
          </div>
        </div>
        <script src="bootstrap5/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <!--<script>
          document.getElementById('downloadLoginPdfBtn').addEventListener('click', function() {
            const resultCard = document.querySelector('.result-card');
            html2pdf().set({
              margin: 0.5,
              filename: 'my_result.pdf',
              image: {
                type: 'jpeg',
                quality: 0.98
              },
              html2canvas: {
                scale: 2
              },
              jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
              }
            }).from(resultCard).save();
          });
        </script>-->
        <script>
          // Prevent right-click context menu
          document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
          });
        </script>
      </div>
    </div>
  </div>
</body>

</html>