<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$teacherId = $input['teacher_id'] ?? '';
$courseTypeId = $input['course_type_id'] ?? '';

if (!$teacherId || !$courseTypeId) {
    echo json_encode(['status' => 'error', 'message' => 'Teacher and course required']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM teacher_courses WHERE teacher_id = ? AND course_type_id = ?");
$stmt->execute([$teacherId, $courseTypeId]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'success', 'message' => 'Already assigned']);
    exit;
}


$stmt = $pdo->prepare("SELECT id FROM teacher_courses WHERE teacher_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$teacherId]);
$last = $stmt->fetch();
$counter = 1;
if ($last) {
    $lastId = $last['id'];
    if (strpos($lastId, "TC{$teacherId}{$courseTypeId}") === 0) {
        $counter = (int)substr($lastId, strlen("TC{$teacherId}{$courseTypeId}")) + 1;
    }
}
$id = "TC{$teacherId}{$courseTypeId}" . sprintf('%03d', $counter);

$stmt = $pdo->prepare("INSERT INTO teacher_courses (id, teacher_id, course_type_id) VALUES (?, ?, ?)");
if ($stmt->execute([$id, $teacherId, $courseTypeId])) {
    echo json_encode(['status' => 'success', 'message' => 'Teacher assigned to course']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to assign teacher']);
}
?>