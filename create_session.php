<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$courseTypeId = $input['course_type_id'] ?? '';
$teacherId = $input['teacher_id'] ?? '';
$sessionDate = $input['session_date'] ?? '';
$startTime = $input['start_time'] ?? '';
$endTime = $input['end_time'] ?? '';
$location = $input['location'] ?? '';
if (!$courseTypeId || !$teacherId || !$sessionDate || !$startTime || !$endTime) {
    echo json_encode(['status' => 'error', 'message' => 'All fields required']);
    exit;
}
$id = 'SES' . sprintf('%03d', rand(1, 999));
$stmt = $pdo->prepare("INSERT INTO course_sessions (id, course_type_id, teacher_id, session_date, start_time, end_time, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
if ($stmt->execute([$id, $courseTypeId, $teacherId, $sessionDate, $startTime, $endTime, $location])) {
    echo json_encode(['status' => 'success', 'message' => 'Session created', 'id' => $id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create session']);
}
?>