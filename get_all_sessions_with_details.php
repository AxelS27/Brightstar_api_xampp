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
        cs.teacher_id
    FROM course_sessions cs
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON cs.teacher_id = t.id
    JOIN users ut ON t.id = ut.id
    ORDER BY cs.session_date, cs.start_time
");
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sessions as &$session) {
    $stmt2 = $pdo->prepare("
        SELECT u.full_name 
        FROM enrollments e
        JOIN students s ON e.student_id = s.id
        JOIN users u ON s.id = u.id
        WHERE e.session_id = ?
    ");
    $stmt2->execute([$session['session_id']]);
    $students = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $session['enrolledStudents'] = $students;
}

echo json_encode(['status' => 'success', 'data' => $sessions]);
?>