<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /project/admin/');
    exit;
}

// Revoke cookie/session (same logic as API logout)
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

header('Location: /project/login.html');
exit;

