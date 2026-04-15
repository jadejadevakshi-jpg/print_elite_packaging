<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: /project/index.php');
    exit;
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '_bootstrap.php';

// Same logic as api/auth/logout.php but redirects to home
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

header('Location: /project/index.php');
exit;

