<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$adminId = require_admin();
$body = read_json_body();

$orderId = (int)($body['order_id'] ?? 0);
$newStatus = trim((string)($body['status'] ?? ''));
$note = trim((string)($body['note'] ?? ''));

$allowed = ['new','processing','printing','ready','dispatched','delivered','cancelled'];
if ($orderId <= 0 || !in_array($newStatus, $allowed, true)) {
    json_response(['ok' => false, 'error' => 'Invalid request'], 400);
}

$pdo = db();
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = :id FOR UPDATE");
    $stmt->execute([':id' => $orderId]);
    $row = $stmt->fetch();
    if (!$row) {
        $pdo->rollBack();
        json_response(['ok' => false, 'error' => 'Order not found'], 404);
    }
    $old = (string)$row['status'];

    $upd = $pdo->prepare("UPDATE orders SET status = :s WHERE id = :id");
    $upd->execute([':s' => $newStatus, ':id' => $orderId]);

    $hist = $pdo->prepare(
        "INSERT INTO order_status_history (order_id, old_status, new_status, changed_by, note)
         VALUES (:oid, :old, :new, :by, :note)"
    );
    $hist->execute([
        ':oid' => $orderId,
        ':old' => $old,
        ':new' => $newStatus,
        ':by' => $adminId,
        ':note' => $note !== '' ? $note : null,
    ]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['ok' => false, 'error' => 'Update failed'], 500);
}

json_response(['ok' => true]);

