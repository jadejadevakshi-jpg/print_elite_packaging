<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$pdo = db();
$stmt = $pdo->query(
    "SELECT id, question, answer, sort_order
     FROM faqs
     WHERE is_active = 1
     ORDER BY sort_order ASC, id ASC"
);
$rows = $stmt->fetchAll();

json_response(['ok' => true, 'faqs' => $rows]);

