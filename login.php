<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';
$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
$password = $input['password'] ?? '';
if (!$id || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'ID and password required']);
    exit;
}
$stmt = $pdo->prepare("SELECT password_hash, role FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user['password_hash'])) {
    echo json_encode(['status' => 'success', 'role' => $user['role'], 'id' => $id]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID or password']);
}
?>