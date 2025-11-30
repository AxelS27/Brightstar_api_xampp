<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';
$studentId = $_GET['student_id'] ?? '';
if (!$studentId) {
    echo json_encode(['status' => 'error', 'message' => 'Student ID required']);
    exit;
}
$stmt = $pdo->prepare("DELETE FROM student_courses WHERE student_id = ?");
$stmt->execute([$studentId]);
echo json_encode(['status' => 'success']);
?>