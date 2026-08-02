<?php
session_start();

$host = "localhost";
$db_name = "ranggo_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>