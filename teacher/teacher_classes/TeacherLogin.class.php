<?php

class TeacherLogin
{

    public function getTeacher($pdo, $email, $password)
    {
        // Database retrieval logic here
        $sql = "SELECT * FROM staffs WHERE email = :email AND staff_role = 'Teacher'";
        // Execute query and return result
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($teacher && password_verify($password, $teacher['pwd'])) {
            return true;
        } else {
            return false;
        }
    }

    public function errorHandler($email,$password)
    {
        // Error handling logic here
        if (empty($email) || empty($password)) {
            return true;
        }else{
            return false;
        }
    }

    public function authenticate($pdo, $email, $password)
    {
        // Authentication logic here
        if ($this->errorHandler($email,$password)) {
            return false;
        } elseif ($this->getTeacher($pdo, $email, $password)) {
            return true;
        }
    }
}
