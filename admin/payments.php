<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    admin_require_login();
    $pdo = db();
    $orderId = (int)($_POST['order_id'] ?? 0);
    $customerEmail = trim((string)($_POST['customer_email'] ?? ''));
    $amount = (float)($_POST['amount'] ?? 0);
    $method = (string)($_POST['method'] ?? 'cash');
    $reference = trim((string)($_POST['reference'] ?? ''));
    $note = trim((string)($_POST['note'] ?? ''));

    $allowedMethods = ['cash','upi','bank_transfer','cheque','other'];
    if ($amount > 0 && in_array($method, $allowedMethods, true)) {
        $customerId = null;
        if ($customerEmail !== '') {
            $u = $pdo->prepare("SELECT id FROM users WHERE email = :e LIMIT 1");
            $u->execute([':e' => $customerEmail]);
            $row = $u->fetch();
            if ($row) $customerId = (int)$row['id'];
        }
        $stmt = $pdo->prepare(
            "INSERT INTO payments (order_id, customer_id, amount, currency, method, reference, note, received_at)
             VALUES (:oid, :cid, :amt, 'INR', :m, :ref, :note, NOW())"
        );
        $stmt->execute([
            ':oid' => $orderId > 0 ? $orderId : null,
            ':cid' => $customerId,
            ':amt' => number_format($amount, 2, '.', ''),
            ':m' => $method,
            ':ref' => $reference !== '' ? $reference : null,
            ':note' => $note !== '' ? $note : null,
        ]);
    }

    header('Location: /project/admin/payments.php');
    exit;
}

admin_render('Payments', 'payments', function (PDO $pdo): void {
    $rows = $pdo->query(
        "SELECT p.id, p.amount, p.currency, p.method, p.reference, p.note, p.received_at,
                o.order_no, u.email AS customer_email
         FROM payments p
         LEFT JOIN orders o ON o.id = p.order_id
         LEFT JOIN users u ON u.id = p.customer_id
         ORDER BY p.received_at DESC
         LIMIT 250"
    )->fetchAll();
    $total = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) s FROM payments")->fetch()['s'];
    ?>
    <div class="grid">
      <div class="card">
        <div class="k">Total received</div>
        <div class="v">INR <?php echo number_format($total, 2); ?></div>
        <div class="hint">Record manual payments (cash, UPI, transfer) and link them to orders.</div>
      </div>
      <div class="card">
        <div class="k">Quick actions</div>
        <div class="hint" style="margin-top:10px;display:flex;gap:10px;flex-wrap:wrap">
          <a class="btn" href="/project/admin/orders.php">Manage orders</a>
          <a class="btn" href="/project/admin/enquiries.php">View enquiries</a>
        </div>
      </div>
      <div class="card"><div class="k">Tips</div><div class="hint">Use Order ID to keep revenue reporting accurate.</div></div>
      <div class="card"><div class="k">Export</div><div class="hint">Coming next: CSV export.</div></div>
    </div>

    <div class="card" style="margin-top:14px">
      <div class="k">Record payment</div>
      <form method="post" style="margin-top:10px">
        <div class="form-row">
          <div><label>Order ID (optional)</label><input type="number" name="order_id" min="0" placeholder="e.g. 1" /></div>
          <div class="span2"><label>Customer email (optional)</label><input type="text" name="customer_email" placeholder="customer@example.com" /></div>
          <div><label>Amount</label><input type="number" step="0.01" min="0" name="amount" required /></div>
          <div>
            <label>Method</label>
            <select name="method">
              <option value="cash">cash</option>
              <option value="upi">upi</option>
              <option value="bank_transfer">bank_transfer</option>
              <option value="cheque">cheque</option>
              <option value="other">other</option>
            </select>
          </div>
          <div><label>Reference</label><input type="text" name="reference" placeholder="Txn/Ref (optional)" /></div>
          <div class="span2"><label>Note</label><input type="text" name="note" placeholder="optional" /></div>
          <div><button class="btn btn-primary" type="submit">Save</button></div>
        </div>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Received</th>
            <th>Order</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Reference</th>
            <th>Note</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo (int)$r['id']; ?></td>
              <td><?php echo admin_h((string)$r['received_at']); ?></td>
              <td><?php echo admin_h((string)($r['order_no'] ?? '')); ?></td>
              <td><?php echo admin_h((string)($r['customer_email'] ?? '')); ?></td>
              <td><?php echo admin_h((string)$r['currency']); ?> <?php echo number_format((float)$r['amount'], 2); ?></td>
              <td><span class="badge"><?php echo admin_h((string)$r['method']); ?></span></td>
              <td><?php echo admin_h((string)($r['reference'] ?? '')); ?></td>
              <td><?php echo admin_h((string)($r['note'] ?? '')); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
});

