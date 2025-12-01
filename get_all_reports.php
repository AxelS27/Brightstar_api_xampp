<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$courseName = $_GET['course'] ?? null;
$teacherName = $_GET['teacher'] ?? null;
$studentName = $_GET['student'] ?? null;

$sql = "
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
";

$where = [];
$params = [];

if ($startDate) {
    $where[] = "cs.session_date >= :startDate";
    $params[':startDate'] = $startDate;
}
if ($endDate) {
    $where[] = "cs.session_date <= :endDate";
    $params[':endDate'] = $endDate;
}
if ($courseName && $courseName !== 'All Courses') {
    $where[] = "ct.name = :courseName";
    $params[':courseName'] = $courseName;
}
if ($teacherName && $teacherName !== 'All Teachers') {
    $where[] = "ut.full_name = :teacherName";
    $params[':teacherName'] = $teacherName;
}
if ($studentName && $studentName !== 'All Students') {
    $where[] = "us.full_name = :studentName";
    $params[':studentName'] = $studentName;
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY cs.session_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/brightstar_api/uploads/';
foreach ($reports as &$r) {
    if (!empty($r['picture'])) {
        $r['picture'] = $baseUrl . basename($r['picture']);
    }
    $r['time'] = $r['startTime'] . ' - ' . $r['endTime'];
}
echo json_encode(['status' => 'success', 'data' => $reports]);
?>