<?php
// api/add_food.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';
session_start();

// simple authorization: require logged in (adjust to your roles)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Accept JSON or form POST
$input = $_POST;
if (empty($input)) {
    // try JSON body
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $input = $decoded;
}

$title = trim($input['title'] ?? '');
$food_type = trim($input['food_type'] ?? '');
$portion = trim($input['portion_size'] ?? '');
$storage = trim($input['storage_instructions'] ?? '');
$quantity = intval($input['quantity'] ?? 1);
$price_raw = $input['price'] ?? null;
$price = ($price_raw === '' || $price_raw === null) ? null : floatval($price_raw);
$expires_at_raw = $input['expires_at'] ?? '';

$errors = [];
if ($title === '') $errors[] = 'Title is required.';
if ($food_type === '') $errors[] = 'Food type is required.';
if ($quantity <= 0) $errors[] = 'Quantity must be at least 1.';
if ($expires_at_raw === '') $errors[] = 'Expiry datetime is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Validate datetime format and 24-hour rule
$expires_at = DateTime::createFromFormat('Y-m-d\TH:i', $expires_at_raw) ?: DateTime::createFromFormat('Y-m-d H:i:s', $expires_at_raw);
if (!$expires_at) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid expires_at format. Use YYYY-MM-DDTHH:MM (HTML datetime-local).']);
    exit;
}

$now = new DateTime();
$max = (clone $now)->add(new DateInterval('PT24H'));
if ($expires_at <= $now) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Expiry must be in the future.']);
    exit;
}
if ($expires_at > $max) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Expiry cannot be more than 24 hours from now.']);
    exit;
}

// optional: associate with session user id if present
$hotel_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

try {
    $stmt = $pdo->prepare("INSERT INTO food_items (hotel_id, title, food_type, portion_size, storage_instructions, quantity, price, expires_at, listed_at, status) VALUES (:hotel_id, :title, :food_type, :portion_size, :storage_instructions, :quantity, :price, :expires_at, NOW(), 'available')");
    $stmt->execute([
        ':hotel_id' => $hotel_id,
        ':title' => $title,
        ':food_type' => $food_type,
        ':portion_size' => $portion,
        ':storage_instructions' => $storage,
        ':quantity' => $quantity,
        ':price' => $price,
        ':expires_at' => $expires_at->format('Y-m-d H:i:s'),
    ]);
    $newId = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $newId]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to insert item.']);
}
