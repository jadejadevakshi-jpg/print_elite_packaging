<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

require_admin();

$pdo = db();
$rows = $pdo->query(
    "SELECT o.id, o.order_no, o.status, o.total_amount, o.currency, o.created_at, u.email AS customer_email, u.name AS customer_name
     FROM orders o
     JOIN users u ON u.id = o.customer_id
     ORDER BY o.created_at DESC
     LIMIT 200"
)->fetchAll();

json_response(['ok' => true, 'orders' => $rows]);

