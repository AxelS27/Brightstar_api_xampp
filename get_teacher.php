<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID required']);
    exit;
}

$stmt = $pdo->prepare("SELECT u.full_name, u.phone, u.date_of_birth, u.profile_image FROM users u WHERE u.id = ? AND u.role = 'teacher'");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    echo json_encode([
        'status' => 'success',
        'data' => [
            'teacherId' => $id,
            'teacherName' => $row['full_name'],
            'teacherPhone' => $row['phone'],
            'teacherDOB' => $row['date_of_birth'],
            'profile_image' => $row['profile_image']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Teacher not found']);
}
?>