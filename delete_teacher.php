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
$pdo->beginTransaction();
try {
    $pdo->exec("DELETE FROM users WHERE id = '$id'");
    $pdo->exec("DELETE FROM teachers WHERE id = '$id'");
    $pdo->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $pdo->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete']);
}
?>