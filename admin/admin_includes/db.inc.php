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
    $host = 'ftpupload.net';
    $database = "ezyro_42312822_questportal";
    $username = 'ezyro_42312822';
    $password = '75516cbdf2c78';
}
$dsn = "mysql:host=$host;dbname=$database";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
