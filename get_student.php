<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID required']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT u.full_name, u.phone, u.date_of_birth, u.profile_image, s.grade_level
    FROM users u
    JOIN students s ON u.id = s.id
    WHERE u.id = ? AND u.role = 'student'
");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    echo json_encode([
        'status' => 'success',
        'data' => [
            'studentId' => $id,
            'studentName' => $row['full_name'],
            'studentPhone' => $row['phone'],
            'studentDOB' => $row['date_of_birth'],
            'gradeLevel' => $row['grade_level'],
            'profile_image' => $row['profile_image']
        ]
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Student not found']);
}
?>