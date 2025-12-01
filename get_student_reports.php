<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$studentId = $_GET['studentId'] ?? '';
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if (!$studentId) {
    echo json_encode(['status' => 'error', 'message' => 'studentId required']);
    exit;
}

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
        u.full_name AS teacherName
    FROM reports r
    JOIN course_sessions cs ON r.session_id = cs.id
    JOIN course_types ct ON cs.course_type_id = ct.id
    JOIN teachers t ON r.teacher_id = t.id
    JOIN users u ON t.id = u.id
    WHERE r.student_id = :studentId
";

$params = [':studentId' => $studentId];

if ($startDate) {
    $sql .= " AND cs.session_date >= :startDate";
    $params[':startDate'] = $startDate;
}
if ($endDate) {
    $sql .= " AND cs.session_date <= :endDate";
    $params[':endDate'] = $endDate;
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