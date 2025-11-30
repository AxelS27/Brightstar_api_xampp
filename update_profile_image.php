<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
require_once 'db.php';

$userId = $_POST['user_id'] ?? '';
if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'User ID required']);
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No image uploaded']);
    exit;
}

$ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $userId . '.' . strtolower($ext);
$target = $uploadDir . $filename;

if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
    $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
    if ($stmt->execute([$filename, $userId])) {
        echo json_encode(['status' => 'success', 'message' => 'Profile image updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
}
?>