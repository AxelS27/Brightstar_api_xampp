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
        ut.full_name AS teacherName,
        ct.id AS course_type_id,
        cs.teacher_id,
        s.id AS student_id,
        us.full_name AS studentName,
        CASE WHEN r.id IS NOT NULL THEN '1' ELSE '0' END AS hasReport,
        r.title,
        r.description,
        r.image_url AS picture
    FROM course_sessions cs
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON cs.teacher_id = t.id
    JOIN users ut ON t.id = ut.id
    JOIN enrollments e ON cs.id = e.session_id
    JOIN students s ON e.student_id = s.id
    JOIN users us ON s.id = us.id
    LEFT JOIN reports r ON cs.id = r.session_id AND s.id = r.student_id
    ORDER BY cs.session_date, cs.start_time, us.full_name
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $data]);
?>