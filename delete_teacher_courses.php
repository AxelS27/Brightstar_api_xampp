<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$teacherId = $_GET['teacher_id'] ?? '';
if (!$teacherId) {
    echo json_encode(['status' => 'error', 'message' => 'Teacher ID required']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM teacher_courses WHERE teacher_id = ?");
if ($stmt->execute([$teacherId])) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>