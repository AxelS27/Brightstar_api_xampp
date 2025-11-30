<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once 'db.php';

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$sessionId = $_POST['session_id'] ?? '';
$studentId = $_POST['student_id'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

if (!$sessionId || !$studentId || !$title || !$description) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    exit;
}

$stmt = $pdo->prepare("SELECT teacher_id FROM course_sessions WHERE id = ?");
$stmt->execute([$sessionId]);
$session = $stmt->fetch();
if (!$session) {
    echo json_encode(['status' => 'error', 'message' => 'Session not found']);
    exit;
}
$teacherId = $session['teacher_id'];

$imageUrl = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'report_' . uniqid() . '.' . strtolower($ext);
    $target = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $imageUrl = $filename;
    }
}

$stmt = $pdo->prepare("SELECT id FROM reports WHERE session_id = ? AND student_id = ?");
$stmt->execute([$sessionId, $studentId]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare("UPDATE reports SET title = ?, description = ?, image_url = ?, updated_at = NOW() WHERE session_id = ? AND student_id = ?");
    $stmt->execute([$title, $description, $imageUrl, $sessionId, $studentId]);
} else {
    $reportId = 'REP' . uniqid();
    $stmt = $pdo->prepare("INSERT INTO reports (id, session_id, student_id, teacher_id, title, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$reportId, $sessionId, $studentId, $teacherId, $title, $description, $imageUrl]);
}

echo json_encode(['status' => 'success', 'message' => 'Report saved']);
?>