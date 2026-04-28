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
    .hero-section-result {
      background: linear-gradient(135deg, var(--quest-green) 0%, var(--quest-green-400) 40%, var(--quest-yellow) 100%);
      border-radius: var(--radius-xl);
      padding: 2rem;
      position: relative;
      overflow: hidden;
      margin-bottom: 2rem;
      animation: fadeInUp 0.5s ease forwards;
    }
    .hero-section-result::before {
      content: ''; position: absolute; top: -50%; right: -20%;
      width: 300px; height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
      border-radius: 50%;
    }
    .hero-content-result { position: relative; z-index: 1; }

    .result-card {
      background: #fff;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-md);
      padding: 2rem;
      animation: fadeInUp 0.5s ease 0.1s both;
    }

    .result-header {
      border-bottom: 1px solid var(--slate-100);
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
    }

    .profile-avatar-result {
      width: 80px; height: 80px; object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255,255,255,0.6);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .result-table th, .result-table td {
      vertical-align: middle;
    }

    .btn-view-all {
      background: linear-gradient(135deg, var(--quest-green), var(--quest-yellow));
      color: #fff; border: none;
      transition: all var(--transition-base);
      position: relative; overflow: hidden;
    }
    .btn-view-all:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-glow-green);
      color: #fff;
    }
  </style>
</head>

<body>
  <?php include "header.php" ?>
  <?php include "sidebar.php" ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-lg-9 py-4">
        <!-- Hero Section -->
        <div class="hero-section-result">
          <div class="hero-content-result d-flex align-items-center justify-content-between">
            <div>
              <h2 class="text-white mb-1 fw-bold">Student Result</h2>
              <p class="mb-0 text-white" style="opacity:0.9">Name: <strong><?php echo $studentData["fullname"] ?></strong></p>
              <p class="mb-0 text-white" style="opacity:0.9">Class: <strong><?php echo $studentData["class"] ?></strong></p>
            </div>
            <img src="<?php echo $studentData["picture"] ?>" alt="Student Avatar" class="profile-avatar-result">
          </div>

        <div class="result-card">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 fw-bold"><i class="bi bi-journal-text text-green me-2"></i>Academic Results</h4>
            <a href="view_result.php" class="btn btn-view-all"><i class="bi bi-eye me-2"></i>View All</a>
          </div>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
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
                    <td colspan="2" class="text-center py-5">
                      <i class="bi bi-file-earmark-x fs-2 text-muted mb-2 d-block"></i>
                      <div class="text-muted">No results yet. Check back later</div>
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
                    <td class="text-muted">
                      <?php echo date('M j, Y', strtotime($result["added_on"])) ?>
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
          </div>

        <script>
          document.addEventListener('contextmenu', function (e) { e.preventDefault(); });
        </script>
      </div>
  </div>
</body>

</html>
