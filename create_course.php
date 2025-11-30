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
$createdBy = $input['created_by'] ?? '';

if (!$id || !$name) {
    echo json_encode(['status' => 'error', 'message' => 'Course ID and name are required']);
    exit;
}

if (!preg_match('/^[a-zA-Z]{2}[0-9]{3}$/', $id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID format. Must be 2 letters followed by 3 digits (e.g., CO001)']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO course_types (id, name, description, created_by) VALUES (?, ?, ?, ?)");

if ($stmt->execute([$id, $name, $description, $createdBy])) {
    echo json_encode(['status' => 'success', 'message' => 'Course created', 'id' => $id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create course']);
}
?>