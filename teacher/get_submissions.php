<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if (!isset($_SESSION["teacher"]) || !isset($_GET['assignment_id'])) {
    http_response_code(403);
    exit();
}

$email = $_SESSION["teacher"];
$teacher = new Teacher($email);
$teacherData = $teacher->getTeacherData($pdo, $email);
$assignmentId = (int)$_GET['assignment_id'];

// Verify the assignment belongs to this teacher
$query = "SELECT id, title FROM assignments WHERE id = :id AND created_by = :teacher_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $assignmentId);
$stmt->bindParam(':teacher_id', $teacherData['id']);
$stmt->execute();

if (!$stmt->fetch()) {
    http_response_code(403);
    exit();
}

$submissions = $teacher->getSubmittedAssignments($pdo, $assignmentId);

if (empty($submissions)): ?>
    <div class="text-center py-4">
        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
        <h5>No submissions yet</h5>
        <p class="text-muted">Students haven't submitted this assignment yet.</p>
    </div>
<?php else: ?>
    <div class="submissions-list">
        <?php foreach ($submissions as $submission): ?>
            <div class="submission-item">
                <div class="student-info">
                    <div>
                        <span class="student-name"><?php echo htmlspecialchars($submission['fullname']); ?></span>
                        <span class="text-muted ms-2">(<?php echo htmlspecialchars($submission['admission_number']); ?>)</span>
                    </div>
                    <div class="text-end">
                        <small class="submission-date">
                            Submitted: <?php echo date('M j, Y g:i A', strtotime($submission['submitted_at'])); ?>
                        </small>
                        <?php if (!empty($submission['grade'])): ?>
                            <span class="badge graded-badge ms-2">Graded: <?php echo htmlspecialchars($submission['grade']); ?></span>
                        <?php else: ?>
                            <span class="badge pending-badge ms-2">Pending Grade</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($submission['comments'])): ?>
                    <div class="mb-2">
                        <strong>Student Comments:</strong>
                        <p class="text-muted mb-2"><?php echo nl2br(htmlspecialchars($submission['comments'])); ?></p>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="uploads/assignments/<?php echo htmlspecialchars($submission['submission_file']); ?>"
                           download class="btn download-btn btn-sm">
                            <i class="bi bi-download me-1"></i>Download Submission
                        </a>
                    </div>

                    <?php if (empty($submission['grade'])): ?>
                        <button class="btn btn-outline-primary btn-sm" onclick="toggleGradeForm(<?php echo $submission['id']; ?>)">
                            <i class="bi bi-pencil me-1"></i>Grade
                        </button>
                    <?php else: ?>
                        <div class="text-end">
                            <small class="text-muted">Grade: <strong><?php echo htmlspecialchars($submission['grade']); ?></strong></small>
                            <?php if (!empty($submission['feedback'])): ?>
                                <br><small class="text-muted">Feedback provided</small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Grading Form -->
                <div class="grade-form" id="gradeForm<?php echo $submission['id']; ?>" style="display: none;">
                    <hr>
                    <form onsubmit="return submitGrade(this);">
                        <input type="hidden" name="grade_submission" value="1">
                        <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Grade</label>
                                <input type="text" class="form-control" name="grade" placeholder="e.g., A+, 95%" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Feedback (optional)</label>
                                <textarea class="form-control" name="feedback" rows="2" placeholder="Provide feedback to the student..."></textarea>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary btn-sm me-2" onclick="toggleGradeForm(<?php echo $submission['id']; ?>)">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-circle me-1"></i>Submit Grade
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleGradeForm(submissionId) {
    const form = document.getElementById('gradeForm' + submissionId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function submitGrade(form) {
    const formData = new FormData(form);
    const button = form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;

    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Saving...';
    button.disabled = true;

    fetch('view_assignments.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // Reload the modal content
            const assignmentId = <?php echo $assignmentId; ?>;
            const title = document.getElementById('assignmentTitle').textContent;
            viewSubmissions(assignmentId, title);
        } else {
            button.innerHTML = originalText;
            button.disabled = false;
            alert('Error saving grade. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        alert('Error saving grade. Please try again.');
    });

    return false;
}
</script>
