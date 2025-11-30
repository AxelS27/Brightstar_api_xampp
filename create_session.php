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

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sessionDate)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format']);
    exit;
}

$datePart = str_replace('-', '', $sessionDate);
$prefix = "SES{$courseTypeId}{$datePart}";

$stmt = $pdo->prepare("
    SELECT id FROM course_sessions 
    WHERE id LIKE ? 
    ORDER BY id DESC 
    LIMIT 1
");
$stmt->execute(["{$prefix}%"]);
$last = $stmt->fetch();

$counter = 1;
if ($last) {
    $lastId = $last['id'];
    if (preg_match("/{$prefix}(\d+)$/", $lastId, $matches)) {
        $counter = (int)$matches[1] + 1;
    }
}

$id = $prefix . sprintf('%03d', $counter);

try {
    $stmt = $pdo->prepare("
        INSERT INTO course_sessions (
            id, course_type_id, teacher_id, session_date, start_time, end_time, location
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$id, $courseTypeId, $teacherId, $sessionDate, $startTime, $endTime, $location]);
    echo json_encode(['status' => 'success', 'message' => 'Session created', 'id' => $id]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>