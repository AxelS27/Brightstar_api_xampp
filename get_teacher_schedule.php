<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$teacherId = $_GET['teacherId'] ?? '';
if (!$teacherId) {
    echo json_encode(['status' => 'error', 'message' => 'teacherId required']);
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
        s.id AS student_id,
        u.full_name AS studentName,
        CASE WHEN r.id IS NOT NULL THEN '1' ELSE '0' END AS hasReport
    FROM course_sessions cs
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN enrollments e ON cs.id = e.session_id
    JOIN students s ON e.student_id = s.id
    JOIN users u ON s.id = u.id
    LEFT JOIN reports r ON cs.id = r.session_id AND s.id = r.student_id
    WHERE cs.teacher_id = ?
    ORDER BY cs.session_date, cs.start_time, u.full_name
");
$stmt->execute([$teacherId]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'data' => $schedules]);
?>