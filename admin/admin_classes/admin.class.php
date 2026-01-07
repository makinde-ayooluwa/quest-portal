<?php

class Admin
{
    public $email;

    public function __construct($email)
    {
        $this->email = $email;
    }


    public function adminData($pdo, $email)
    {
        $query = "SELECT * FROM staffs WHERE staff_role = 'Admin' AND email = :email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudents($pdo)
    {
        $query = "SELECT * FROM students";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVerifiedStudents($pdo)
    {
        $query = "SELECT * FROM students WHERE account_verification = 'Verified'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnverifiedStudents($pdo)
    {
        $query = "SELECT * FROM students WHERE account_verification = 'Not verified'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaffs($pdo)
    {
        $query = "SELECT * FROM staffs ORDER BY staff_role DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVerifiedStaffs($pdo)
    {
        $query = "SELECT * FROM staffs WHERE account_verification = 'Verified'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnverifiedStaffs($pdo)
    {
        $query = "SELECT * FROM staffs WHERE account_verification = 'Not verified'";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClasses($pdo)
    {
        $query = "SELECT * FROM classes_names_only";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function studentExists($pdo, $data)
    {
        $query = "SELECT admission_number FROM students WHERE admission_number = :admission_number OR email = :email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":admission_number", $data["admission_number"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addStudent($pdo, $studentData)
    {

        if ($this->studentExists($pdo, $studentData)) {
            return false;
        } else {
            $query = "INSERT INTO students(fullname,email,admission_number,class) VALUES (:fullname,:email,:admission_number,:class)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":fullname", $studentData["fullname"]);
            $stmt->bindParam(":email", $studentData["email"]);
            $stmt->bindParam(":admission_number", $studentData["admission_number"]);
            $stmt->bindParam(":class", $studentData["class"]);
            $stmt->execute();
            return true;
        }
    }

    

    public function addClass($pdo, $class_name, $mentor_name, $class_status)
    {
        function classExists($pdo, $class_name)
        {
            $query = "SELECT * FROM classes WHERE class_name = :class_name";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':class_name', $class_name);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        function staffAssigned($pdo, $staff_name)
        {
            $query = "SELECT * FROM classes WHERE mentor_assigned = :mentor_assigned";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':mentor_assigned', $staff_name);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if (classExists($pdo, $class_name)) {
            $_SESSION['error'] = "Class with this name already exists.";
            header("Location: add_class.php");
            exit();
        } else {
            $query = "INSERT INTO classes (class_name, mentor_name, class_status) VALUES (:class_name, :mentor_name, :class_status)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':class_name', $class_name);
            $stmt->bindParam(':mentor_name', $mentor_name);
            $stmt->bindParam(':class_status', $class_status);
            $stmt->execute();
        }
    }

    public function addStaff($pdo, $fullname, $email, $phone, $gender, $portal_code, $staff_role, $employment_date, $staff_status)
    {
        function staffExists($pdo, $email, $portal_code)
        {
            $query = "SELECT * FROM staffs WHERE email = :email OR portal_code = :portal_code";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':portal_code', $portal_code);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if (staffExists($pdo, $email, $portal_code)) {
            $_SESSION['error'] = "Staff with this email / portal code already exists.";
            header("Location: add_staff.php");
            exit();
        } else {
            $query = "INSERT INTO staffs (fullname, email, phone, gender, portal_code, staff_role, employment_date, staff_status) VALUES (:fullname, :email, :phone, :gender, :portal_code, :staff_role, :employment_date, :staff_status)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fullname', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':portal_code', $portal_code);
            $stmt->bindParam(':staff_role', $staff_role);
            $stmt->bindParam(':employment_date', $employment_date);
            $stmt->bindParam(':staff_status', $staff_status);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function deleteStaff($pdo, $id)
    {
        $query = "DELETE FROM staffs WHERE id = :id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $delete = "DELETE FROM classes WHERE mentor_id_as_staff = :id;";
        $dStmt = $pdo->prepare($delete);
        $dStmt->bindParam(":id", $id);
        $dStmt->execute();
    }

    public function deleteStudent(PDO $pdo, $id)
    {
        $query = "DELETE FROM students WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }

    public function getSpecificStaff($pdo, $id)
    {
        $query = "SELECT * FROM staffs WHERE id = :id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSpecificAdmin($pdo, $portal_code)
    {
        $query = "SELECT * FROM staffs WHERE portal_code = :portal_code AND staff_role = 'Admin'";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":portal_code", $portal_code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateClass($pdo, $class_name, $mentor_name, $class_status, $id)
    {
        $query = "UPDATE classes SET class_name = :class_name, mentor_name = :mentor_name, class_status = :class_status WHERE id = :id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':class_name', $class_name);
        $stmt->bindParam(':mentor_name', $mentor_name);
        $stmt->bindParam(':class_status', $class_status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function viewClass($pdo, $id)
    {
        function getClassName($pdo, $id)
        {
            $query = 'SELECT class_name FROM classes_names_only WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)["class_name"];
        }

        $className = getClassName($pdo, $id);

        $query = "SELECT * FROM students WHERE class = :class";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":class", $className);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassTeacherDetails($pdo, $id)
    {
        function getClass($pdo, $id)
        {
            $query = 'SELECT class_name FROM classes_names_only WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)["class_name"];
        }

        $class_name = getClass($pdo, $id);

        $query = "SELECT * FROM classes WHERE class_name = :class_name";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":class_name", $class_name);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassName($pdo, $id)
    {
        $query = 'SELECT class_name FROM classes_names_only WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)["class_name"];
    }

    // Enhanced dashboard methods for new features

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
        $query = "SELECT * FROM notifications WHERE user_type = 'admin' AND read_at IS NULL ORDER BY created_at DESC LIMIT 10";
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

    public function getSystemHealth($pdo)
    {
        // Basic system health metrics
        $health = [];

        // Database connection status
        $health['db_status'] = 'healthy';

        // Get database size (approximate)
        try {
            $query = "SELECT
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as db_size_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $health['db_size'] = $result['db_size_mb'] . ' MB';
        } catch (Exception $e) {
            $health['db_size'] = 'Unknown';
        }

        // Recent system logs count
        $query = "SELECT COUNT(*) as log_count FROM system_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $health['recent_logs'] = $result['log_count'];

        // Server uptime (simplified)
        $health['server_uptime'] = 'Running';

        return $health;
    }

    public function getAnalyticsData($pdo)
    {
        $analytics = [];

        // Student registration trend (last 12 months)
        $query = "SELECT DATE_FORMAT(added_on, '%Y-%m') as month, COUNT(*) as count
                  FROM students
                  WHERE added_on >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                  GROUP BY DATE_FORMAT(added_on, '%Y-%m')
                  ORDER BY month";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $analytics['student_trend'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Class distribution
        $query = "SELECT class, COUNT(*) as count FROM students GROUP BY class ORDER BY count DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $analytics['class_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Staff role distribution
        $query = "SELECT staff_role, COUNT(*) as count FROM staffs GROUP BY staff_role";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $analytics['staff_roles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $analytics;
    }

    public function logActivity($pdo, $userType, $userId, $action, $details = null)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $query = "INSERT INTO activities (user_type, user_id, action, details, ip_address) VALUES (:user_type, :user_id, :action, :details, :ip)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_type', $userType);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
    }

    public function addNotification($pdo, $userType, $title, $message, $type = 'info')
    {
        $query = "INSERT INTO notifications (user_type, title, message, type) VALUES (:user_type, :title, :message, :type)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_type', $userType);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        return $stmt->execute();
    }

    public function addEvent($pdo, $title, $description, $eventDate, $startTime, $endTime, $location, $createdBy)
    {
        $query = "INSERT INTO events (title, description, event_date, start_time, end_time, location, created_by) VALUES (:title, :description, :event_date, :start_time, :end_time, :location, :created_by)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':event_date', $eventDate);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':created_by', $createdBy);
        return $stmt->execute();
    }

    public function createNotification($pdo, $userType, $userId, $title, $message, $type = 'info')
    {
        $query = "INSERT INTO notifications (user_type, user_id, title, message, type) VALUES (:user_type, :user_id, :title, :message, :type)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_type', $userType);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        $stmt->execute();
    }

    public function searchEntities($pdo, $searchTerm, $entityType = 'all', $limit = 20)
    {
        $results = [];

        if ($entityType === 'all' || $entityType === 'students') {
            $query = "SELECT 'student' as type, id, fullname as name, email, class as additional_info FROM students
                      WHERE fullname LIKE :search OR email LIKE :search OR admission_number LIKE :search
                      LIMIT :limit";
            $stmt = $pdo->prepare($query);
            $searchParam = '%' . $searchTerm . '%';
            $stmt->bindParam(':search', $searchParam);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        if ($entityType === 'all' || $entityType === 'staff') {
            $query = "SELECT 'staff' as type, id, fullname as name, email, staff_role as additional_info FROM staffs
                      WHERE fullname LIKE :search OR email LIKE :search OR staff_role LIKE :search
                      LIMIT :limit";
            $stmt = $pdo->prepare($query);
            $searchParam = '%' . $searchTerm . '%';
            $stmt->bindParam(':search', $searchParam);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        if ($entityType === 'all' || $entityType === 'classes') {
            $query = "SELECT 'class' as type, id, class_name as name, '' as email, '' as additional_info FROM classes_names_only
                      WHERE class_name LIKE :search
                      LIMIT :limit";
            $stmt = $pdo->prepare($query);
            $searchParam = '%' . $searchTerm . '%';
            $stmt->bindParam(':search', $searchParam);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        return $results;
    }

    // Support Request Management Methods

    public function getAllSupportRequests($pdo, $status = null, $priority = null)
    {
        $query = "SELECT s.*, st.fullname as student_name, st.admission_number
                  FROM supports s
                  LEFT JOIN students st ON s.email = st.email";

        $conditions = [];
        $params = [];

        if ($status) {
            $conditions[] = "s.status = :status";
            $params[':status'] = $status;
        }

        if ($priority) {
            $conditions[] = "s.support_priority = :priority";
            $params[':priority'] = $priority;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY s.added_on DESC";

        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSupportRequest($pdo, $id)
    {
        $sql = "DELETE FROM supports WHERE id = :id;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getSupportRequestById($pdo, $id)
    {
        $query = "SELECT s.*, st.fullname as student_name, st.admission_number, st.class,
                         resp.fullname as responded_by_name
                  FROM supports s
                  LEFT JOIN students st ON s.email = st.email
                  LEFT JOIN staffs resp ON s.responded_by = resp.id
                  WHERE s.id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSupportRequestStatus($pdo, $id, $status, $adminResponse = null, $respondedBy = null)
    {
        $query = "UPDATE supports SET status = :status";

        if ($adminResponse !== null) {
            $query .= ", admin_response = :admin_response, responded_by = :responded_by, responded_at = NOW()";
        }

        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        if ($adminResponse !== null) {
            $stmt->bindValue(':admin_response', $adminResponse, PDO::PARAM_STR);
            $stmt->bindValue(':responded_by', $respondedBy, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    public function getSupportStats($pdo)
    {
        $query = "SELECT
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_requests,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_requests,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_requests,
                    SUM(CASE WHEN support_priority = 'urgent' THEN 1 ELSE 0 END) as urgent_requests,
                    SUM(CASE WHEN support_priority = 'critical' THEN 1 ELSE 0 END) as critical_requests
                  FROM supports";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}


class AdminSetup
{

    public $pdo;
    public $data;

    public function __construct($pdo, $data)
    {

        $this->pdo = $pdo;
        $this->data = $data;
    }

    /*$data = [
        "portal_code"=>$portal_code,
        "email"=>$email,
        "picture"=>$picture,
        "password"=>$password
    ]; */


    public function staffGotten()
    {

        $sql = "SELECT * FROM staffs WHERE email = :email AND portal_code = :portal_code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":email", $this->data["email"]);
        $stmt->bindParam(":portal_code", $this->data["portal_code"]);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            return true;
        } else {
            return false;
        }
    }

    public function errorHandler()
    {

        if (empty($this->data["portal_code"]) || empty($this->data["email"]) || empty($this->data["picture"]) || empty($this->data["password"])) {
            return true;
        } elseif (!$this->staffGotten()) {
            return true;
        } else {
            return false;
        }
    }

    public function run()
    {
        if ($this->errorHandler()) {
            return false;
        } else {
            $run = $this->pdo->prepare("UPDATE staffs SET picture = :picture,pwd = :pwd,account_verification = 'Verified' WHERE email = :email AND portal_code = :portal_code AND staff_role = 'Admin'");
            $run->bindParam(":picture", $this->data["picture_path"]);
            $run->bindParam(":pwd", $this->data["hashedPassword"]);
            $run->bindParam(":email", $this->data["email"]);
            $run->bindParam(":portal_code", $this->data["portal_code"]);
            $run->execute();
            return true;
        }
    }
}
