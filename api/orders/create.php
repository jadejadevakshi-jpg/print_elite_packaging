<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$uid = require_auth();

$body = read_json_body();
$items = $body['items'] ?? [];
$notes = trim((string)($body['notes'] ?? ''));

if (!is_array($items) || count($items) < 1) {
    json_response(['ok' => false, 'error' => 'At least one item is required'], 400);
}

$cleanItems = [];
foreach ($items as $it) {
    if (!is_array($it)) continue;
    $name = trim((string)($it['product_name'] ?? ''));
    $sku = trim((string)($it['sku'] ?? ''));
    $qty = (int)($it['quantity'] ?? 0);
    $unit = (float)($it['unit_price'] ?? 0);
    if ($name === '' || $qty <= 0) continue;
    if ($unit < 0) $unit = 0;
    $cleanItems[] = [
        'product_name' => $name,
        'sku' => $sku !== '' ? $sku : null,
        'quantity' => $qty,
        'unit_price' => $unit,
        'line_total' => $qty * $unit,
    ];
}
if (!$cleanItems) {
    json_response(['ok' => false, 'error' => 'Invalid items'], 400);
}

$subtotal = 0.0;
foreach ($cleanItems as $ci) $subtotal += (float)$ci['line_total'];
$tax = 0.0;
$total = $subtotal + $tax;

$pdo = db();
$pdo->beginTransaction();
try {
    $orderNo = 'EP' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    $ins = $pdo->prepare(
        "INSERT INTO orders (customer_id, order_no, status, notes, subtotal_amount, tax_amount, total_amount, currency)
         VALUES (:cid, :no, 'new', :notes, :sub, :tax, :tot, 'INR')"
    );
    $ins->execute([
        ':cid' => $uid,
        ':no' => $orderNo,
        ':notes' => $notes !== '' ? $notes : null,
        ':sub' => number_format($subtotal, 2, '.', ''),
        ':tax' => number_format($tax, 2, '.', ''),
        ':tot' => number_format($total, 2, '.', ''),
    ]);

    $orderId = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_name, sku, quantity, unit_price, line_total)
         VALUES (:oid, :pn, :sku, :q, :up, :lt)"
    );
    foreach ($cleanItems as $ci) {
        $itemStmt->execute([
            ':oid' => $orderId,
            ':pn' => $ci['product_name'],
            ':sku' => $ci['sku'],
            ':q' => $ci['quantity'],
            ':up' => number_format((float)$ci['unit_price'], 2, '.', ''),
            ':lt' => number_format((float)$ci['line_total'], 2, '.', ''),
        ]);
    }

    $hist = $pdo->prepare(
        "INSERT INTO order_status_history (order_id, old_status, new_status, changed_by, note)
         VALUES (:oid, NULL, 'new', :by, 'Created by customer')"
    );
    $hist->execute([':oid' => $orderId, ':by' => $uid]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Order creation failed'], 500);
}

json_response(['ok' => true, 'order' => ['id' => $orderId, 'order_no' => $orderNo]]);

