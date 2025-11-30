<?php
require_once 'db.php';

$users = [
  ['id' => 'S150807001', 'password' => 'S150807001'],
  ['id' => 'S031208001', 'password' => 'S031208001'],
  ['id' => 'S221107001', 'password' => 'S221107001'],
  ['id' => 'T270206001', 'password' => 'T270206001'],
  ['id' => 'T100395001', 'password' => 'T100395001'],
  ['id' => 'ADM001', 'password' => 'ADM001'],
];

foreach ($users as $user) {
  $hash = password_hash($user['password'], PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
  $stmt->execute([$hash, $user['id']]);
  echo "Updated: {$user['id']}\n";
}
?>