<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? '';
$name = $input['name'] ?? '';
$description = $input['description'] ?? '';

if (!$id || !$name) {
    echo json_encode(['status' => 'error', 'message' => 'ID and name required']);
    exit;
}

$stmt = $pdo->prepare("UPDATE course_types SET name = ?, description = ? WHERE id = ?");
if ($stmt->execute([$name, $description, $id])) {
    echo json_encode(['status' => 'success', 'message' => 'Course updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update course']);
}
?>