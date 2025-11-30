<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

$stmt = $pdo->query("
    SELECT 
        u.id,
        u.full_name,
        u.date_of_birth,
        u.phone,
        u.email,
        s.grade_level,
        u.profile_image
    FROM users u
    JOIN students s ON u.id = s.id
    WHERE u.role = 'student'
");

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $students]);
?>