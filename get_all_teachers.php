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
        t.subject_specialization AS specialization,
        u.profile_image
    FROM users u
    JOIN teachers t ON u.id = t.id
    WHERE u.role = 'teacher'
");

$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $teachers]);
?>