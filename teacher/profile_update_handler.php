<?php
session_start();
include "teacher_includes/autoloader.inc.php";
include "teacher_includes/db.inc.php";
include "teacher_includes/teacher.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $email = $_SESSION['teacher'];

    // Handle file upload
    $picturePath = null;
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/teachers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['picture']['name']);
        $targetFile = $uploadDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['picture']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFile)) {
                $picturePath = 'assets/uploads/teachers/' . $fileName;
            }
        }
    }

    // Update database
    try {
        $sql = "UPDATE staffs SET fullname = :fullname, phone = :phone";
        $params = [':fullname' => $fullname, ':phone' => $phone, ':email' => $email];

        if ($picturePath) {
            $sql .= ", picture = :picture";
            $params[':picture'] = $picturePath;
        }

        $sql .= " WHERE email = :email AND staff_role = 'Teacher'";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = 'Profile updated successfully!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to update profile. Please try again.';
    }

    header('Location: profile.php');
    exit();
}
?>
