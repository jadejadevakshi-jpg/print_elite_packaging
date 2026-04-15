<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';

admin_render('Dashboard', 'dashboard', function (PDO $pdo): void {
    $revenue = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) s FROM payments")->fetch()['s'];
    $ordersTotal = (int)$pdo->query("SELECT COUNT(*) c FROM orders")->fetch()['c'];
    $ordersInProgress = (int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE status IN ('new','processing','printing','ready','dispatched')")->fetch()['c'];
    $delivered = (int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE status = 'delivered'")->fetch()['c'];
    $unpaid = (float)$pdo->query(
        "SELECT COALESCE(SUM(o.total_amount),0) - COALESCE((SELECT SUM(p.amount) FROM payments p WHERE p.order_id IS NOT NULL),0) s
         FROM orders o"
    )->fetch()['s'];
    $enqNew = (int)$pdo->query("SELECT COUNT(*) c FROM enquiries WHERE status='new'")->fetch()['c'];
    $customers = (int)$pdo->query("SELECT COUNT(*) c FROM users WHERE role='customer'")->fetch()['c'];
    ?>
    <div class="grid">
      <div class="card"><div class="k">New enquiries</div><div class="v"><?php echo (int)$enqNew; ?></div></div>
      <div class="card"><div class="k">Orders (total)</div><div class="v"><?php echo (int)$ordersTotal; ?></div></div>
      <div class="card"><div class="k">In progress</div><div class="v"><?php echo (int)$ordersInProgress; ?></div></div>
      <div class="card"><div class="k">Delivered</div><div class="v"><?php echo (int)$delivered; ?></div></div>
    </div>

    <div class="grid">
      <div class="card"><div class="k">Revenue received</div><div class="v">INR <?php echo number_format($revenue, 2); ?></div></div>
      <div class="card"><div class="k">Unpaid estimate</div><div class="v">INR <?php echo number_format(max(0.0, $unpaid), 2); ?></div></div>
      <div class="card"><div class="k">Customers</div><div class="v"><?php echo (int)$customers; ?></div></div>
      <div class="card"><div class="k">System</div><div class="v">Online</div><div class="hint">XAMPP / MySQL connected</div></div>
    </div>
    <?php
});

