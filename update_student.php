<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID required']);
    exit;
}
$stmt = $pdo->prepare("UPDATE users SET full_name = ?, date_of_birth = ?, phone = ? WHERE id = ?");
$stmt->execute([$input['full_name'], $input['date_of_birth'], $input['phone'], $id]);
$stmt = $pdo->prepare("UPDATE students SET grade_level = ? WHERE id = ?");
$stmt->execute([$input['grade_level'], $id]);
echo json_encode(['status' => 'success']);
?>