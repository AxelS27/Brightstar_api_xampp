<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$sessionId = $_GET['session_id'] ?? '';
$studentId = $_GET['student_id'] ?? '';

if (!$sessionId || !$studentId) {
    echo json_encode(['status' => 'error', 'message' => 'session_id and student_id required']);
    exit;
}

$stmt = $pdo->prepare("SELECT title, description, image_url AS picture FROM reports WHERE session_id = ? AND student_id = ? LIMIT 1");
$stmt->execute([$sessionId, $studentId]);
$report = $stmt->fetch();

if ($report) {
    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/brightstar_api/uploads/';
    if (!empty($report['picture'])) {
        $report['picture'] = $baseUrl . basename($report['picture']);
    }
    echo json_encode(['status' => 'success', 'data' => $report]);
} else {
    echo json_encode(['status' => 'success', 'data' => null]);
}
?>