<?php
$host = 'localhost';
$dsn = "mysql:host=$host;dbname=questportal";
$name = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $name, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
