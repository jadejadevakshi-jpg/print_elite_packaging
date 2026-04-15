<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
$name = '';
$email = '';
$password = '';

if (strpos($contentType, 'application/json') !== false) {
    $body = read_json_body();
    $name = trim((string)($body['name'] ?? ''));
    $email = trim((string)($body['email'] ?? ''));
    $password = (string)($body['password'] ?? '');
} else {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
}

if ($name === '' || $email === '' || strlen($password) < 8) {
    json_response(['ok' => false, 'error' => 'Name, email, and password (8+ chars) are required'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok' => false, 'error' => 'Invalid email'], 400);
}

$pdo = db();

// Ensure not already registered
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Email already registered'], 409);
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$ins = $pdo->prepare("INSERT INTO users (email, password_hash, name, role, is_active) VALUES (:e,:h,:n,'customer',1)");
$ins->execute([':e' => $email, ':h' => $hash, ':n' => $name]);

json_response(['ok' => true]);

