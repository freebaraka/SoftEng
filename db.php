<?php
// db.php - Database connection
$host = 'localhost';
$dbname = 'SaveEat';
$username = 'root';
$password = 'munyoiks7';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    // Don't output sensitive info in production
    die("Database connection failed");
}