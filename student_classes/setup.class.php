<?php

class Setup
{
    public $data;
    public $pdo;

    public function __construct($data, $pdo)
    {
        $this->data = $data;
        $this->pdo = $pdo;
    }

    public function setup($pdo, $data)
    {
        function getId($pdo, $admission_number)
        {
            $query = "SELECT * FROM students WHERE admission_number = :admission_number;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":admission_number", $admission_number);
            $stmt->execute();
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return true;
            } else {
                return false;
            }
        }

        function update($pdo, $data)
        {
            $query = "UPDATE students SET dob = :dob, gender = :gender,
            picture = :picture, phone = :phone, home_address = :home_address,
            father_name = :father_name, father_email = :father_email, father_phone = :father_phone,
            mother_name = :mother_name, mother_email = :mother_email, mother_phone = :mother_phone,
            pwd = :pwd, account_verification = 'Verified' WHERE admission_number = :admission_number AND email = :email;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":dob", $data["dob"]);
            $stmt->bindParam(":gender", $data["gender"]);
            $stmt->bindParam(":picture", $data["picture"]);
            $stmt->bindParam(":phone", $data["phone"]);
            $stmt->bindParam(":home_address", $data["home_address"]);
            $stmt->bindParam(":father_name", $data["father_name"]);
            $stmt->bindParam(":father_email", $data["father_email"]);
            $stmt->bindParam(":father_phone", $data["father_phone"]);
            $stmt->bindParam(":mother_name", $data["mother_name"]);
            $stmt->bindParam(":mother_email", $data["mother_email"]);
            $stmt->bindParam(":mother_phone", $data["mother_phone"]);
            $stmt->bindParam(":admission_number", $data["admission_number"]);
            $stmt->bindParam(":email", $data["email"]);
            $stmt->bindParam(":pwd", $data["hashedPwd"]);
            $stmt->execute();
        }

        if (getId($pdo, $data["admission_number"])) {
            update($pdo, $data);
        }
    }
}
