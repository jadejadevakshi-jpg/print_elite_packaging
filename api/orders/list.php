<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$uid = require_auth();
$pdo = db();

$stmt = $pdo->prepare(
    "SELECT id, order_no, status, notes, subtotal_amount, tax_amount, total_amount, currency, created_at, updated_at
     FROM orders
     WHERE customer_id = :cid
     ORDER BY created_at DESC
     LIMIT 100"
);
$stmt->execute([':cid' => $uid]);
$orders = $stmt->fetchAll();

json_response(['ok' => true, 'orders' => $orders]);

