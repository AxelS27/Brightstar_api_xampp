<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$adminId = $_GET['id'] ?? '';
if (!$adminId) {
    echo json_encode(['status' => 'error', 'message' => 'ID required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT full_name AS adminName, profile_image 
    FROM users 
    WHERE id = ? AND role = 'admin'
");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if ($admin) {
    echo json_encode(['status' => 'success', 'data' => $admin]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Admin not found']);
}
?>