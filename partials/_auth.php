<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '_bootstrap.php';

function current_user(): ?array
{
    $uid = session_get_user_id();
    if ($uid === null) return null;
    $pdo = db();
    $stmt = $pdo->prepare("SELECT id, email, name, role, is_active FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $uid]);
    $u = $stmt->fetch();
    if (!$u || (int)$u['is_active'] !== 1) return null;
    return $u;
}

function require_customer(): array
{
    $u = current_user();
    if (!$u) {
        header('Location: /project/login.html');
        exit;
    }
    return $u;
}

