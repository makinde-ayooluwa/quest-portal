<?php

class Login {
    public $email;

    public function __construct($email) {
        $this->email = $email;
    }
    public function getAdmin($pdo, $email, $password)
    {
        $query = "SELECT * FROM staffs WHERE staff_role = 'Admin' AND email = :email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($password, $result["pwd"])) {
            return true;
        } else {
            return false;
        }
    }
}