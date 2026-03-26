<?php

include "student_includes/db.inc.php";

$sql = file_get_contents("./database/db.sql");

// Split queries
$queries = explode(";", $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        $pdo->exec($query);
    }
}

echo "Database tables created successfully." . PHP_EOL;