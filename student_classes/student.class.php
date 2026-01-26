<?php

class Student
{
    public $username_email;
    public function __construct($username_email)
    {
        $this->username_email = $username_email;
    }

    public function getStudent($pdo, $email)
    {
        $query = "SELECT * FROM students WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStudent($pdo, $data)
    {



        $databaseData = $this->getStudent($pdo, $data["email"]);
        if (password_verify($data["pwd"], $databaseData["pwd"])) {
            $query = "UPDATE students SET gender = :gender, phone = :phone,
        home_address = :home_address,father_name = :father_name,
        father_phone = :father_phone,father_email = :father_email,
        mother_name = :mother_name,mother_phone = :mother_phone,
        mother_email = :mother_email WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":gender", $data["gender"]);
            $stmt->bindParam(":phone", $data["phone"]);
            $stmt->bindParam(":home_address", $data["home_address"]);
            $stmt->bindParam(":father_name", $data["father_name"]);
            $stmt->bindParam(":father_phone", $data["father_phone"]);
            $stmt->bindParam(":father_email", $data["father_email"]);
            $stmt->bindParam(":mother_name", $data["mother_name"]);
            $stmt->bindParam(":mother_phone", $data["mother_phone"]);
            $stmt->bindParam(":mother_email", $data["mother_email"]);
            $stmt->bindParam(":id", $data["id"]);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function changePassword($pdo, $data)
    {

        $dbData = $this->getStudent($pdo, $data["email"]);

        function errorHandler($data, $dbData)
        {
            if (empty($data["oldPwd"]) || empty($data["newPwd"]) || empty($data["confirmPwd"])) {
                return true;
            } elseif (!password_verify($data["oldPwd"], $dbData["pwd"])) {
                return true;
            } elseif (!($data["newPwd"] == $data["confirmPwd"])) {
                return true;
            } else {
                return false;
            }
        }

        function changePassword($pdo, $data, $dbData)
        {
            $query = "UPDATE students SET pwd = :pwd WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":pwd", $data["hashedPwd"]);
            $stmt->bindParam(":email", $dbData["email"]);
            $stmt->execute();
        }

        if (errorHandler($data, $dbData)) {
            return false;
        } else {
            changePassword($pdo, $data, $dbData);
            return true;
        }
    }

    public function changePicture($pdo, $data)
    {
        $dbData = $this->getStudent($pdo, $data["email"]);

        function errorHandle($data, $dbData)
        {
            if (empty($data["picture"]) || empty($data["pwd"])) {
                return true;
            } elseif (!password_verify($data["pwd"], $dbData["pwd"])) {
                return true;
            } else {
                return false;
            }
        }



        function changePicture($pdo, $data, $dbData)
        {
            $sql = "UPDATE students SET picture = :picture WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":picture", $data["fullPath"]);
            $stmt->bindParam(":email", $dbData["email"]);
            $stmt->execute();
        }

        if (errorHandle($data, $dbData)) {
            return false;
        } else {
            changePicture($pdo, $data, $dbData);
            return true;
        }
    }

    public function raiseSupport($pdo, $support_data)
    {

        function errors($support_data)
        {

            if (empty($support_data["support_topic"]) || empty($support_data["support_priority"]) || empty($support_data["support_subject"]) || empty($support_data["support_description"]) || empty($support_data["email"])) {
                return true;
            } elseif (!filter_var($support_data["email"], FILTER_VALIDATE_EMAIL)) {
                return true;
            } else {
                return false;
            }
        }

        function runSupport($pdo, $support_data)
        {
            $sql = "INSERT INTO supports(support_topic, support_priority, support_subject, support_description, email, phone) VALUES (:support_topic, :support_priority, :support_subject, :support_description, :email, :phone)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":support_topic", $support_data["support_topic"]);
            $stmt->bindParam(":support_priority", $support_data["support_priority"]);
            $stmt->bindParam(":support_subject", $support_data["support_subject"]);
            $stmt->bindParam(":support_description", $support_data["support_description"]);
            $stmt->bindParam(":email", $support_data["email"]);
            $stmt->bindParam(":phone", $support_data["phone"]);
            $stmt->execute();
        }

        if (errors($support_data)) {
            return false;
        } else {
            runSupport($pdo, $support_data);
            return true;
        }
    }
}
