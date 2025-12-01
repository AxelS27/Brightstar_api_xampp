<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$courseIds = $input['course_ids'] ?? [];

if (empty($courseIds)) {
    echo json_encode(['status' => 'error', 'message' => 'Course IDs required']);
    exit;
}

$placeholders = str_repeat('?,', count($courseIds) - 1) . '?';
$stmt = $pdo->prepare("SELECT id, name FROM course_types WHERE id IN ($placeholders)");
$stmt->execute($courseIds);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $courses]);
?>