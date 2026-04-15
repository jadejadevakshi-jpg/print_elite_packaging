<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

require_admin();

$body = read_json_body();
$orderId = isset($body['order_id']) ? (int)$body['order_id'] : 0;
$customerId = isset($body['customer_id']) ? (int)$body['customer_id'] : 0;
$amount = (float)($body['amount'] ?? 0);
$method = (string)($body['method'] ?? 'cash');
$reference = trim((string)($body['reference'] ?? ''));
$note = trim((string)($body['note'] ?? ''));

$allowedMethods = ['cash','upi','bank_transfer','cheque','other'];
if ($amount <= 0 || !in_array($method, $allowedMethods, true)) {
    json_response(['ok' => false, 'error' => 'Invalid payment'], 400);
}

$pdo = db();
$stmt = $pdo->prepare(
    "INSERT INTO payments (order_id, customer_id, amount, currency, method, reference, note, received_at)
     VALUES (:oid, :cid, :amt, 'INR', :m, :ref, :note, NOW())"
);
$stmt->execute([
    ':oid' => $orderId > 0 ? $orderId : null,
    ':cid' => $customerId > 0 ? $customerId : null,
    ':amt' => number_format($amount, 2, '.', ''),
    ':m' => $method,
    ':ref' => $reference !== '' ? $reference : null,
    ':note' => $note !== '' ? $note : null,
]);

json_response(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);

