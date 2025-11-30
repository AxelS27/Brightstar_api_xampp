<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$studentId = $_GET['studentId'] ?? '';
if (!$studentId) {
    echo json_encode(['status' => 'error', 'message' => 'studentId required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        cs.id AS session_id,
        ct.name AS courseName,
        cs.session_date AS courseDate,
        cs.start_time AS startTime,
        cs.end_time AS endTime,
        cs.location AS room,
        u.full_name AS teacherName
    FROM enrollments e
    JOIN course_sessions cs ON e.session_id = cs.id
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON cs.teacher_id = t.id
    JOIN users u ON t.id = u.id
    WHERE e.student_id = ?
    ORDER BY cs.session_date, cs.start_time
");
$stmt->execute([$studentId]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'data' => $schedules]);
?>