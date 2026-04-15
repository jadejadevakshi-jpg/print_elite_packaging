<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '_bootstrap.php';

function admin_require_login(): int
{
    $uid = session_get_user_id();
    if ($uid === null) {
        header('Location: /project/login.html');
        exit;
    }
    $pdo = db();
    $stmt = $pdo->prepare("SELECT role, is_active FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $uid]);
    $u = $stmt->fetch();
    if (!$u || (int)$u['is_active'] !== 1) {
        header('Location: /project/login.html');
        exit;
    }
    if (($u['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
    return $uid;
}

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

