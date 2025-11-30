<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$stmt = $pdo->query("
    SELECT 
        r.title,
        r.description,
        r.image_url AS picture,
        cs.session_date AS courseDate,
        cs.start_time AS startTime,
        cs.end_time AS endTime,
        cs.location AS room,
        ct.name AS courseName,
        ut.full_name AS teacherName,
        us.full_name AS studentName
    FROM reports r
    JOIN course_sessions cs ON r.session_id = cs.id
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON r.teacher_id = t.id
    JOIN users ut ON t.id = ut.id
    JOIN students s ON r.student_id = s.id
    JOIN users us ON s.id = us.id
    ORDER BY cs.session_date DESC
");

$reports = $stmt->fetchAll();
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/brightstar_api/uploads/';
foreach ($reports as &$r) {
    if (!empty($r['picture'])) {
        $r['picture'] = $baseUrl . basename($r['picture']);
    }
    $r['time'] = $r['startTime'] . ' - ' . $r['endTime'];
}
echo json_encode(['status' => 'success', 'data' => $reports]);
?>