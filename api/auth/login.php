<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

// Accept both form POST (login.html) and JSON (API clients)
$email = '';
$password = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
if (strpos($contentType, 'application/json') !== false) {
    $body = read_json_body();
    $email = trim((string)($body['email'] ?? ''));
    $password = (string)($body['password'] ?? '');
} else {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
}

if ($email === '' || strlen($password) < 8) {
    json_response(['ok' => false, 'error' => 'Invalid credentials'], 400);
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || (int)$user['is_active'] !== 1 || !password_verify($password, (string)$user['password_hash'])) {
    json_response(['ok' => false, 'error' => 'Invalid credentials'], 401);
}

$token = random_token(32);
$expiresAt = (new DateTimeImmutable('+7 days'))->format('Y-m-d H:i:s');

$pdo->beginTransaction();
try {
    $ins = $pdo->prepare(
        "INSERT INTO auth_sessions (user_id, token_hash, ip, user_agent, expires_at)
         VALUES (:uid, :th, :ip, :ua, :exp)"
    );
    $ins->execute([
        ':uid' => (int)$user['id'],
        ':th' => token_hash($token),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ':exp' => $expiresAt,
    ]);

    $upd = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
    $upd->execute([':id' => (int)$user['id']]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Login failed'], 500);
}

setcookie('EP_SESSION', $token, [
    'expires' => time() + 7 * 24 * 60 * 60,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);

json_response([
    'ok' => true,
    'user' => [
        'id' => (int)$user['id'],
        'email' => (string)$user['email'],
        'role' => (string)$user['role'],
    ],
]);

