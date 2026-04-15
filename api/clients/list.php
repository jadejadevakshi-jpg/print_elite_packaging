<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$pdo = db();
$stmt = $pdo->query(
    "SELECT id, name, website, logo_url, sort_order
     FROM clients
     WHERE is_active = 1
     ORDER BY sort_order ASC, name ASC"
);
$rows = $stmt->fetchAll();

json_response(['ok' => true, 'clients' => $rows]);

