<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$studentId = $input['student_id'] ?? '';
$courseTypeId = $input['course_type_id'] ?? '';
if (!$studentId || !$courseTypeId) {
    echo json_encode(['status' => 'error', 'message' => 'Student and course required']);
    exit;
}
$stmt = $pdo->prepare("SELECT id FROM student_courses WHERE student_id = ? AND course_type_id = ?");
$stmt->execute([$studentId, $courseTypeId]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Already enrolled']);
    exit;
}
$stmt = $pdo->prepare("SELECT MAX(id) as max_id FROM student_courses");
$stmt->execute();
$row = $stmt->fetch();
$maxId = $row['max_id'];
$counter = 1;
if ($maxId && strpos($maxId, $studentId . $courseTypeId) === 0) {
    $counter = (int)substr($maxId, strlen($studentId . $courseTypeId)) + 1;
}
$id = 'ENR' . $studentId . $courseTypeId . sprintf('%03d', $counter);
$stmt = $pdo->prepare("INSERT INTO student_courses (id, student_id, course_type_id) VALUES (?, ?, ?)");
if ($stmt->execute([$id, $studentId, $courseTypeId])) {
    echo json_encode(['status' => 'success', 'message' => 'Student assigned to course']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to assign student']);
}
?>