<?php
// api/get_foods.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

try {
    $stmt = $pdo->prepare("SELECT id, title, food_type, portion_size, storage_instructions, quantity, price, listed_at, expires_at, status FROM food_items WHERE status = 'available' ORDER BY listed_at DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
