<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$studentId = $input['student_id'] ?? '';
$sessionId = $input['session_id'] ?? '';
if (!$studentId || !$sessionId) {
    echo json_encode(['status' => 'error', 'message' => 'Student and session required']);
    exit;
}
$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND session_id = ?");
$stmt->execute([$studentId, $sessionId]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Already enrolled']);
    exit;
}
$stmt = $pdo->prepare("SELECT MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)) as max_num FROM enrollments");
$stmt->execute();
$row = $stmt->fetch();
$counter = $row['max_num'] ? $row['max_num'] + 1 : 1;
$id = 'ENR' . sprintf('%05d', $counter);
$stmt = $pdo->prepare("INSERT INTO enrollments (id, student_id, session_id) VALUES (?, ?, ?)");
if ($stmt->execute([$id, $studentId, $sessionId])) {
    echo json_encode(['status' => 'success', 'message' => 'Student enrolled']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to enroll student']);
}
?>