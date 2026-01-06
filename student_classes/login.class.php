<?php

class Login
{
    public $username;
    public $password;
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function validate($pdo, $username, $password)
    {
        $query = "SELECT * FROM students WHERE email = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // ✅ Prevent warning by checking $result first
        if (!$result) {
            return false;
        }

        // ✅ Only verify password if $result is valid
        if (isset($result['pwd']) && password_verify($password, $result['pwd'])) {
            return true;
        }
    }
}
