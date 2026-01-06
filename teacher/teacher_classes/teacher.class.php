<?php

class Teacher
{
    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getTeacherData($pdo, $email)
    {
        $query = "SELECT * FROM staffs WHERE email = :email AND staff_role = 'Teacher'";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAssignedClasses($pdo, $teacherEmail)
    {
        $query = "SELECT c.*, cn.class_name FROM classes c JOIN classes_names_only cn ON c.class_name = cn.class_name WHERE c.mentor_email = :email ORDER BY id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $teacherEmail);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsInClass($pdo, $className)
    {
        $query = "SELECT * FROM students WHERE class = :class";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':class', $className);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postAssignment($pdo, $data)
    {
        // data: title, description, subject, class_name, due_date, file_path, created_by
        $query = "INSERT INTO assignments (title, description, subject, class_name, due_date, file_path, created_by) VALUES (:title, :description, :subject, :class_name, :due_date, :file_path, :created_by)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':subject', $data['subject']);
        $stmt->bindParam(':class_name', $data['class_name']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':file_path', $data['file_path']);
        $stmt->bindParam(':created_by', $data['created_by']);
        return $stmt->execute();
    }

    public function getAssignmentsForClass($pdo, $className, $teacherId)
    {
        $query = "SELECT * FROM assignments WHERE class_name = :class_name AND created_by = :created_by ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':class_name', $className);
        $stmt->bindParam(':created_by', $teacherId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubmittedAssignments($pdo, $assignmentId)
    {
        $query = "SELECT s.*, st.fullname, st.admission_number FROM assignment_submissions s JOIN students st ON s.student_id = st.id WHERE s.assignment_id = :assignment_id ORDER BY s.submitted_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':assignment_id', $assignmentId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function gradeSubmission($pdo, $submissionId, $grade, $feedback)
    {
        $query = "UPDATE assignment_submissions SET grade = :grade, feedback = :feedback, status = 'graded' WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->bindParam(':id', $submissionId);
        return $stmt->execute();
    }

    public function postResult($pdo, $data)
    {
        // data: academic_term, student_admission_number, result_file
        $query = "INSERT INTO results (academic_term, student_admission_number, result_file, added_on) VALUES (:academic_term, :student_admission_number, :result_file, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':academic_term', $data['academic_term']);
        $stmt->bindParam(':student_admission_number', $data['student_admission_number']);
        $stmt->bindParam(':result_file', $data['result_file']);
        return $stmt->execute();
    }

    public function promoteStudent($pdo, $studentId, $newClass)
    {
        $query = "UPDATE students SET class = :class WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':class', $newClass);
        $stmt->bindParam(':id', $studentId);
        return $stmt->execute();
    }

    public function demoteStudent($pdo, $studentId, $newClass)
    {
        // Assuming demote means move to lower class, same as promote but different class
        return $this->promoteStudent($pdo, $studentId, $newClass);
    }

    public function getAllClasses($pdo)
    {
        $query = "SELECT class_name FROM classes_names_only";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentAssignments($pdo, $studentId, $teacherId)
    {
        $query = "SELECT a.title, a.subject, a.due_date, a.created_at,
                         s.id as submission_id, s.grade, s.feedback, s.submitted_at
                  FROM assignments a
                  LEFT JOIN assignment_submissions s ON a.id = s.assignment_id AND s.student_id = :student_id
                  WHERE a.created_by = :teacher_id AND a.class_name = (SELECT class FROM students WHERE id = :student_id)
                  ORDER BY a.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':teacher_id', $teacherId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpecificTeacher($pdo, $portal_code)
    {
        $query = "SELECT * FROM staffs WHERE portal_code = :portal_code AND staff_role = 'Teacher'";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":portal_code", $portal_code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResultsForTeacher($pdo, $teacherEmail)
    {
        // Get all students in teacher's assigned classes
        $assignedClasses = $this->getAssignedClasses($pdo, $teacherEmail);
        $classNames = array_column($assignedClasses, 'class_name');

        if (empty($classNames)) {
            return [];
        }

        // Build IN clause for class names
        $placeholders = str_repeat('?,', count($classNames) - 1) . '?';

        $query = "SELECT r.*, s.fullname, s.class, s.admission_number FROM results r
                  JOIN students s ON r.student_admission_number = s.admission_number
                  WHERE s.class IN ($placeholders)
                  ORDER BY r.added_on DESC";

        $stmt = $pdo->prepare($query);
        $stmt->execute($classNames);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteResult($pdo, $resultId, $teacherEmail)
    {
        // First, get the result to check permissions
        $query = "SELECT r.*, s.class FROM results r
                  JOIN students s ON r.student_admission_number = s.admission_number
                  WHERE r.id = :result_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':result_id', $resultId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false; // Result not found
        }

        // Check if teacher is assigned to this student's class
        $assignedClasses = $this->getAssignedClasses($pdo, $teacherEmail);
        $classNames = array_column($assignedClasses, 'class_name');

        if (!in_array($result['class'], $classNames)) {
            return false; // Teacher not authorized for this class
        }

        // Delete the file if it exists
        $filePath = '../assets/uploads/results/' . $result['result_file'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $deleteQuery = "DELETE FROM results WHERE id = :result_id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindParam(':result_id', $resultId);
        return $deleteStmt->execute();
    }

    public function getRecentActivities($pdo, $limit = 10)
    {
        $query = "SELECT * FROM activities ORDER BY timestamp DESC LIMIT :limit";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadNotifications($pdo)
    {
        $query = "SELECT * FROM notifications WHERE user_type = 'teacher' AND read_at IS NULL ORDER BY created_at DESC LIMIT 10";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingEvents($pdo, $limit = 5)
    {
        $query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC, start_time ASC LIMIT :limit";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


class TeacherSetup {

    public $pdo;
    public $data;

    public function __construct($pdo, $data)
    {
        $this->pdo = $pdo;
        $this->data = $data;
    }

    public function staffGotten(){

        $sql = "SELECT * FROM staffs WHERE email = :email AND portal_code = :portal_code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":email",$this->data["email"]);
        $stmt->bindParam(":portal_code",$this->data["portal_code"]);
        $stmt->execute();
        if($stmt->fetch(PDO::FETCH_ASSOC)){
            return true;
        }else{
            return false;
        }

    }

    public function errorHandler(){

        if(empty($this->data["portal_code"]) || empty($this->data["email"])|| empty($this->data["picture"]["name"])|| empty($this->data["password"])){
            return true;
        }elseif(!$this->staffGotten()){
            return true;
        }else{
            return false;
        }

    }

    public function run(){
        if($this->errorHandler()){
            return false;
        }else{
            $run = $this->pdo->prepare("UPDATE staffs SET picture = :picture,pwd = :pwd,account_verification = 'Verified' WHERE email = :email AND portal_code = :portal_code AND staff_role = 'Teacher'");
            $run->bindParam(":picture",$this->data["picture_path"]);
            $run->bindParam(":pwd",$this->data["hashedPassword"]);
            $run->bindParam(":email",$this->data["email"]);
            $run->bindParam(":portal_code",$this->data["portal_code"]);
            $run->execute();
            return true;
        }
    }

}