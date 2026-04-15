<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('X-Frame-Options: SAMEORIGIN');

if (PHP_VERSION_ID < 70400) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'PHP 7.4+ is required.'], JSON_UNESCAPED_SLASHES);
    exit;
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') return [];
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) return [];
    return $decoded;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $cfgPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    $cfg = require $cfgPath;

    $host = $cfg['host'] ?? '127.0.0.1';
    $port = (int)($cfg['port'] ?? 3306);
    $name = $cfg['name'] ?? 'elite_print';
    $user = $cfg['user'] ?? 'root';
    $pass = $cfg['pass'] ?? '';
    $charset = $cfg['charset'] ?? 'utf8mb4';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function random_token(int $bytes = 32): string
{
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

function token_hash(string $token): string
{
    return hash('sha256', $token);
}

function session_get_user_id(): ?int
{
    if (empty($_COOKIE['EP_SESSION'])) return null;
    $token = (string)$_COOKIE['EP_SESSION'];
    if ($token === '') return null;

    $pdo = db();
    $stmt = $pdo->prepare(
        "SELECT user_id
         FROM auth_sessions
         WHERE token_hash = :h
           AND revoked_at IS NULL
           AND expires_at > NOW()
         LIMIT 1"
    );
    $stmt->execute([':h' => token_hash($token)]);
    $row = $stmt->fetch();
    if (!$row) return null;
    return (int)$row['user_id'];
}

function require_auth(): int
{
    $uid = session_get_user_id();
    if ($uid === null) {
        json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
    }
    return $uid;
}

function require_admin(): int
{
    $uid = require_auth();
    $pdo = db();
    $stmt = $pdo->prepare("SELECT role, is_active FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $uid]);
    $u = $stmt->fetch();
    if (!$u || (int)$u['is_active'] !== 1) {
        json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
    }
    if (($u['role'] ?? '') !== 'admin') {
        json_response(['ok' => false, 'error' => 'Forbidden'], 403);
    }
    return $uid;
}

