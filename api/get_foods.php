<?php
// api/get_foods.php
header('Content-Type: application/json; charset=utf-8');

// Include the database connection properly
require_once __DIR__ . '/../db.php'; // Adjust path as needed

try {
    // Create database connection if not already created
    if (!isset($pdo)) {
        $pdo = new PDO("mysql:host=localhost;dbname=SaveEat", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    $stmt = $pdo->prepare("SELECT id, title, food_type, portion_size, storage_instructions, quantity, price, listed_at, expires_at, status FROM food_items WHERE status = 'available' ORDER BY listed_at DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}