<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $adminId = admin_require_login();
    $pdo = db();
    $id = (int)($_POST['id'] ?? 0);
    $status = (string)($_POST['status'] ?? 'new');
    $note = trim((string)($_POST['note'] ?? ''));
    $allowed = ['new','processing','printing','ready','dispatched','delivered','cancelled'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $pdo->beginTransaction();
        try {
            $cur = $pdo->prepare("SELECT status FROM orders WHERE id = :id FOR UPDATE");
            $cur->execute([':id' => $id]);
            $r = $cur->fetch();
            if ($r) {
                $old = (string)$r['status'];
                $upd = $pdo->prepare("UPDATE orders SET status = :s WHERE id = :id");
                $upd->execute([':s' => $status, ':id' => $id]);

                $hist = $pdo->prepare(
                    "INSERT INTO order_status_history (order_id, old_status, new_status, changed_by, note)
                     VALUES (:oid, :old, :new, :by, :note)"
                );
                $hist->execute([
                    ':oid' => $id,
                    ':old' => $old,
                    ':new' => $status,
                    ':by' => $adminId,
                    ':note' => $note !== '' ? $note : null,
                ]);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
        }
    }
    header('Location: /project/admin/orders.php');
    exit;
}

admin_render('Orders', 'orders', function (PDO $pdo): void {
    $rows = $pdo->query(
        "SELECT o.id, o.order_no, o.status, o.total_amount, o.currency, o.created_at,
                u.email AS customer_email, u.name AS customer_name
         FROM orders o
         JOIN users u ON u.id = o.customer_id
         ORDER BY o.created_at DESC
         LIMIT 250"
    )->fetchAll();
    ?>
    <div class="hint">Update order status as it moves through processing, printing, dispatch and delivery.</div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Total</th>
            <th>Created</th>
            <th>Update</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <strong><?php echo admin_h((string)$r['order_no']); ?></strong>
                <div class="muted">#<?php echo (int)$r['id']; ?></div>
              </td>
              <td>
                <div><?php echo admin_h((string)($r['customer_name'] ?? '')); ?></div>
                <div class="muted"><?php echo admin_h((string)$r['customer_email']); ?></div>
              </td>
              <td><span class="badge"><?php echo admin_h((string)$r['status']); ?></span></td>
              <td><?php echo admin_h((string)$r['currency']); ?> <?php echo number_format((float)$r['total_amount'], 2); ?></td>
              <td><?php echo admin_h((string)$r['created_at']); ?></td>
              <td>
                <form method="post" style="margin:0;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                  <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                  <select name="status" style="min-width:160px">
                    <?php foreach (['new','processing','printing','ready','dispatched','delivered','cancelled'] as $s): ?>
                      <option value="<?php echo admin_h($s); ?>" <?php echo ($r['status'] === $s) ? 'selected' : ''; ?>><?php echo admin_h($s); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <input type="text" name="note" placeholder="note (optional)" style="min-width:220px" />
                  <button class="btn btn-primary" type="submit">Save</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
});

