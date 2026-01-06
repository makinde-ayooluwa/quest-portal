<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";

// Check if user is logged in (use the same session key set by login_handler.php)
if (!isset($_SESSION["admin"])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode(['students' => [], 'staff' => [], 'classes' => []]);
    exit();
}

$results = [
    'students' => [],
    'staff' => [],
    'classes' => []
];

try {
    // Search students
    $studentQuery = "SELECT * FROM students WHERE fullname LIKE :query OR email LIKE :query LIMIT 5";
    $stmt = $pdo->prepare($studentQuery);
    $stmt->execute(['query' => '%' . $query . '%']);
    $results['students'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search staff
    $staffQuery = "SELECT id, fullname, staff_role FROM staffs WHERE fullname LIKE :query OR email LIKE :query LIMIT 5";
    $stmt = $pdo->prepare($staffQuery);
    $stmt->execute(['query' => '%' . $query . '%']);
    $results['staff'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search classes
    $classQuery = "SELECT id, class_name FROM classes_names_only WHERE class_name LIKE :query LIMIT 5";
    $stmt = $pdo->prepare($classQuery);
    $stmt->execute(['query' => '%' . $query . '%']);
    $results['classes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
    exit();
}

echo json_encode($results);
?>
