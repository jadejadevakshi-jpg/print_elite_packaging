<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

if (!empty($_COOKIE['EP_SESSION'])) {
    $token = (string)$_COOKIE['EP_SESSION'];
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE auth_sessions SET revoked_at = NOW() WHERE token_hash = :h");
    $stmt->execute([':h' => token_hash($token)]);
}

setcookie('EP_SESSION', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

json_response(['ok' => true]);

