<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
$courseTypeId = $input['course_type_id'] ?? '';
$teacherId = $input['teacher_id'] ?? '';
$sessionDate = $input['session_date'] ?? '';
$startTime = $input['start_time'] ?? '';
$endTime = $input['end_time'] ?? '';
$location = $input['location'] ?? '';
if (!$id || !$courseTypeId || !$teacherId || !$sessionDate || !$startTime || !$endTime) {
    echo json_encode(['status' => 'error', 'message' => 'All fields required']);
    exit;
}
$stmt = $pdo->prepare("UPDATE course_sessions SET course_type_id = ?, teacher_id = ?, session_date = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?");
if ($stmt->execute([$courseTypeId, $teacherId, $sessionDate, $startTime, $endTime, $location, $id])) {
    echo json_encode(['status' => 'success', 'message' => 'Session updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update session']);
}
?>