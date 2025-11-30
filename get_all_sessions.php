<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';
$stmt = $pdo->query("
    SELECT 
        cs.id AS session_id,
        ct.name AS courseName,
        cs.session_date AS courseDate,
        cs.start_time AS startTime,
        cs.end_time AS endTime,
        cs.location AS room,
        u.full_name AS teacherName,
        ct.id AS course_type_id
    FROM course_sessions cs
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON cs.teacher_id = t.id
    JOIN users u ON t.id = u.id
    ORDER BY cs.session_date, cs.start_time
");
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $sessions]);
?>