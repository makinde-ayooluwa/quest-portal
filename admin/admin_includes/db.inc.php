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
    $host = 'sql208.infinityfree.com';
    $database = "if0_42312347_questportal";
    $username = 'if0_42312347';
    $password = 'ck7CW7SqPb';
}
$dsn = "mysql:host=$host;dbname=$database";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
