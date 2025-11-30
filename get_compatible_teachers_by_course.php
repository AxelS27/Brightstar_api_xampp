<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$courseTypeId = $_GET['course_type_id'] ?? '';
if (!$courseTypeId) {
    echo json_encode(['status' => 'error', 'message' => 'Course type ID required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT t.id, u.full_name
    FROM teacher_courses tc
    JOIN teachers t ON tc.teacher_id = t.id
    JOIN users u ON t.id = u.id
    WHERE tc.course_type_id = ?
");
$stmt->execute([$courseTypeId]);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $teachers]);
?>