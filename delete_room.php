<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID required']);
    exit;
}
$stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
if ($stmt->execute([$id])) {
    echo json_encode(['status' => 'success', 'message' => 'Room deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete room']);
}
?>