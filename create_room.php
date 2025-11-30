<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? '';
$capacity = $input['capacity'] ?? 0;
if (!$name) {
    echo json_encode(['status' => 'error', 'message' => 'Room name is required']);
    exit;
}
$stmt = $pdo->prepare("INSERT INTO rooms (name, capacity) VALUES (?, ?)");
if ($stmt->execute([$name, $capacity])) {
    echo json_encode(['status' => 'success', 'message' => 'Room created', 'id' => $pdo->lastInsertId()]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create room']);
}
?>