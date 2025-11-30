<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';
$stmt = $pdo->query("SELECT id, name, capacity FROM rooms");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'data' => $rooms]);
?>