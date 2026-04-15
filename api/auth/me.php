<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$uid = session_get_user_id();
if ($uid === null) {
    json_response(['ok' => true, 'user' => null]);
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id, email, name, role, is_active, last_login_at, created_at FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $uid]);
$u = $stmt->fetch();
if (!$u || (int)$u['is_active'] !== 1) {
    json_response(['ok' => true, 'user' => null]);
}

json_response(['ok' => true, 'user' => [
    'id' => (int)$u['id'],
    'email' => (string)$u['email'],
    'name' => $u['name'],
    'role' => (string)$u['role'],
    'last_login_at' => $u['last_login_at'],
    'created_at' => $u['created_at'],
]]);

