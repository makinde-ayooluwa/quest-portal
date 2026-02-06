<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $className = $_POST["class_name"];
    $mentorEmail = $_POST["mentor_email"];
    $classId = $_SESSION["class_id"];

    /*function mentorIsAssignedToTheClass($pdo, $mentorEmail) {
        $query = "SELECT mentor_email FROM classes WHERE mentor_email = :mentor_email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":mentor_email", $mentor_email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            return true;
        }
        return false;
    }*/

    // Validate and process the form data
    if (empty($className) || empty($mentorEmail)) {
        $_SESSION["error"] = "All fields are required.";
        header("Location: edit_class.php?id=" . $classId);
        exit();
    }/* else if(mentorIsAssigned($pdo,$mentorEmail)) {
        $_SESSION["error"] = "Mentor has been assigned to a class already";
        header("Location: edit_class.php?id=" . $classId);
        exit();
    }*/
     else {
        // Update the class information in the database
        function mentorIsPresent($pdo,$mentorEmail,$className){
            $query = "SELECT * FROM classes WHERE mentor_email = :mentor_email AND class_name = :class_name;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":mentor_email", $mentorEmail);
            $stmt->bindParam(":class_name", $className);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return true;
            }else{
                return false;
            }
        }
        if(mentorIsPresent($pdo,$mentorEmail,$className)){
            $_SESSION["error"] = "The selected staff is already assigned to this class";
        }else{
            $query = "INSERT INTO classes(class_name,mentor_email) VALUES (:class_name,:mentor_email);";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":class_name", $className);
        $stmt->bindParam(":mentor_email", $mentorEmail);
        if ($stmt->execute()) {
            $_SESSION["success"] = "Class updated successfully.";
        } else {
            $_SESSION["error"] = "Failed to update class.";
        }
        }
        
        function getMentorName($pdo, $mentorEmail){
            $query = "SELECT fullname FROM staffs WHERE email = :mentor_email;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":mentor_email", $mentorEmail);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            return $results["fullname"];
        }
        $mentorName = getMentorName($pdo, $mentorEmail);
        $update = "UPDATE classes SET mentor_name = :mentor_name WHERE mentor_email = :mentor_email;";
        $stmt = $pdo->prepare($update);
        $stmt->bindParam(":mentor_name", $mentorName);
        $stmt->bindParam(":mentor_email", $mentorEmail);
        $stmt->execute();

        $idSelect = "SELECT id FROM staffs WHERE email = :mentor_email;";
        $Selectstmt = $pdo->prepare($idSelect);
        $Selectstmt->bindParam(":mentor_email", $mentorEmail);
        $Selectstmt->execute();
        $result = $Selectstmt->fetch(PDO::FETCH_ASSOC);
        $id = $result["id"];

        $idUpdate = "UPDATE classes SET mentor_id_as_staff = :id_as_staff WHERE mentor_email = :mentor_email;";
        $Updatestmt = $pdo->prepare($idUpdate);
        $Updatestmt->bindParam(":id_as_staff", $id);
        $Updatestmt->bindParam(":mentor_email", $mentorEmail);
        $Updatestmt->execute();

        header("Location: edit_class.php?id=" . $classId);
        exit();
    }
} else {
    header("Location: ./");
    exit();
}
