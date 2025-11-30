<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);
$fullName = $input['full_name'] ?? '';
$dob = $input['date_of_birth'] ?? '';
$phone = $input['phone'] ?? '';
$gradeLevel = $input['grade_level'] ?? '';
$email = $input['email'] ?? '';

if (!$fullName || !$dob) {
    echo json_encode(['status' => 'error', 'message' => 'Full name and date of birth are required']);
    exit;
}

$dobParts = explode('-', $dob);
$dd = $dobParts[2];
$mm = $dobParts[1];
$yy = substr($dobParts[0], -2);
$counter = 1;
$id = "S{$dd}{$mm}{$yy}" . sprintf('%03d', $counter);

while (true) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) break;
    $counter++;
    $id = "S{$dd}{$mm}{$yy}" . sprintf('%03d', $counter);
}

$firstName = explode(' ', $fullName)[0];
$hashedPassword = password_hash($id, PASSWORD_DEFAULT);
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (id, username, email, password_hash, full_name, phone, date_of_birth, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $firstName, $email, $hashedPassword, $fullName, $phone, $dob, 'student']);
    $stmt = $pdo->prepare("INSERT INTO students (id, student_id_number, grade_level) VALUES (?, ?, ?)");
    $stmt->execute([$id, $id, $gradeLevel]);
    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Student created', 'id' => $id]);
} catch (Exception $e) {
    $pdo->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>