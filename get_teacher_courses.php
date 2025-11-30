<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';
$teacherId = $_GET['teacher_id'] ?? '';
if (!$teacherId) {
    echo json_encode(['status' => 'error', 'message' => 'Teacher ID required']);
    exit;
}
$stmt = $pdo->prepare("
    SELECT tc.course_type_id
    FROM teacher_courses tc
    WHERE tc.teacher_id = ?
");
$stmt->execute([$teacherId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $courses]);
?>