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
    SELECT s.id, u.full_name
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.id
    JOIN users u ON s.id = u.id
    WHERE sc.course_type_id = ?
");
$stmt->execute([$courseTypeId]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $students]);
?>