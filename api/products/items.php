<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . '_bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$pdo = db();
if ($categoryId > 0) {
    $stmt = $pdo->prepare(
        "SELECT id, category_id, name, description, image_url, sku
         FROM product_items
         WHERE is_active = 1 AND category_id = :cid
         ORDER BY id DESC"
    );
    $stmt->execute([':cid' => $categoryId]);
} else {
    $stmt = $pdo->query(
        "SELECT id, category_id, name, description, image_url, sku
         FROM product_items
         WHERE is_active = 1
         ORDER BY id DESC"
    );
}

$rows = $stmt->fetchAll();
json_response(['ok' => true, 'items' => $rows]);

