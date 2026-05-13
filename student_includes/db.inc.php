<?php
$host = "";
$database  = "";
$username = "";
$password = "";
if ($_SERVER["HTTP_HOST"] == "localhost") {
    $host = 'localhost';
    $database = 'questportal';
    $username = 'root';
    $password = '';
} else {
    $host = 'sql205.infinityfree.com';
    $database = "if0_40847405_questportal";
    $username = 'if0_40847405';
    $password = '1edwGZFqVAm3e';
}
$dsn = "mysql:host=$host;dbname=$database";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
