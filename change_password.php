<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? '';
$currentPassword = $input['current_password'] ?? '';
$newPassword = $input['new_password'] ?? '';

if (!$id || !$currentPassword || !$newPassword) {
    echo json_encode(['status' => 'error', 'message' => 'All fields required']);
    exit;
}

$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

if (!password_verify($currentPassword, $user['password_hash'])) {
    echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
if ($stmt->execute([$newHash, $id])) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Update failed']);
}
?>