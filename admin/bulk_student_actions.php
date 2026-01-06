<?php
session_start();
include "admin_includes/autoloader.inc.php";
include "admin_includes/db.inc.php";
include "admin_includes/admin.inc.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students.php');
    exit();
}

$action = $_POST['action'] ?? '';
$ids = $_POST['selected_ids'] ?? [];

// allow promote action as well
if (!in_array($action, ['delete', 'mark_unverified', 'promote'])) {
    $_SESSION['error'] = 'Invalid bulk action.';
    header('Location: students.php');
    exit();
}

// sanitize ids to integers
$ids = array_filter(array_map('intval', (array)$ids));
if (empty($ids)) {
    $_SESSION['error'] = 'No students selected.';
    header('Location: students.php');
    exit();
}

try {
    $pdo->beginTransaction();

    // Build a parameterized IN clause
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    if ($action === 'delete') {
        // Optionally fetch file paths to unlink pictures if needed
        $delStmt = $pdo->prepare("DELETE FROM students WHERE id IN ($placeholders)");
        $delStmt->execute($ids);
        $_SESSION['success'] = 'Selected students deleted.';
    } elseif ($action === 'mark_unverified') {
        $status = 'Not verified';
        $updStmt = $pdo->prepare("UPDATE students SET account_verification = ? WHERE id IN ($placeholders)");
        $params = array_merge([$status], $ids);
        $updStmt->execute($params);
        $_SESSION['success'] = 'Selected students updated.';
    } elseif ($action === 'promote') {
        $promoteTo = trim((string)($_POST['promote_to'] ?? ''));
        if ($promoteTo === '') {
            $pdo->rollBack();
            $_SESSION["error"] = 'No destination class specified for promotion.';
            header('Location: students.php');
            exit();
        }
        $updStmt = $pdo->prepare("UPDATE students SET class = ? WHERE id IN ($placeholders)");
        $params = array_merge([$promoteTo], $ids);
        $updStmt->execute($params);
    $_SESSION['success'] = 'Selected students promoted / demoted to ' . $promoteTo . '.';
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Bulk student action error: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while processing bulk action.';
}

header('Location: students.php');
exit();
