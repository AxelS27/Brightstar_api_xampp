<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';
$sessionId = $_GET['session_id'] ?? '';
if (!$sessionId) {
    echo json_encode(['status' => 'error', 'message' => 'Session ID required']);
    exit;
}
$stmt = $pdo->prepare("SELECT student_id FROM enrollments WHERE session_id = ?");
$stmt->execute([$sessionId]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $enrollments]);
?>