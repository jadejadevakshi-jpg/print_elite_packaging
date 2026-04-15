<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
$name = '';
$email = '';
$phone = '';
$message = '';
$page = '';

if (strpos($contentType, 'application/json') !== false) {
    $body = read_json_body();
    $name = trim((string)($body['name'] ?? ''));
    $email = trim((string)($body['email'] ?? ''));
    $phone = trim((string)($body['phone'] ?? ''));
    $message = trim((string)($body['message'] ?? ''));
    $page = trim((string)($body['page'] ?? ''));
} else {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $page = trim((string)($_POST['page'] ?? ''));
}

if ($name === '' || $email === '') {
    json_response(['ok' => false, 'error' => 'Name and email are required'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok' => false, 'error' => 'Invalid email'], 400);
}

$pdo = db();
$stmt = $pdo->prepare(
    "INSERT INTO enquiries (source, name, email, phone, message, page, ip, user_agent, status)
     VALUES ('contact_form', :name, :email, :phone, :message, :page, :ip, :ua, 'new')"
);
$stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':phone' => $phone !== '' ? $phone : null,
    ':message' => $message !== '' ? $message : null,
    ':page' => $page !== '' ? $page : null,
    ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ':ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
]);

json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);

