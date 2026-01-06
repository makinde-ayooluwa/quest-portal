<?php

$dsn = 'mysql:host=localhost;dbname=questportal';
$name = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
