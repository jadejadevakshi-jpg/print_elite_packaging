<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    admin_require_login();
    $pdo = db();
    $id = (int)($_POST['id'] ?? 0);
    $action = (string)($_POST['action'] ?? '');

    if ($id > 0 && $action === 'status') {
        $status = (string)($_POST['status'] ?? 'new');
        $allowed = ['new','in_progress','closed','spam'];
        if (!in_array($status, $allowed, true)) $status = 'new';
        $stmt = $pdo->prepare("UPDATE enquiries SET status = :s WHERE id = :id");
        $stmt->execute([':s' => $status, ':id' => $id]);
    } elseif ($id > 0 && $action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    header('Location: /project/admin/enquiries.php');
    exit;
}

admin_render('Enquiries', 'enquiries', function (PDO $pdo): void {
    $rows = $pdo->query(
        "SELECT id, source, name, email, phone, message, page, status, created_at
         FROM enquiries
         ORDER BY created_at DESC
         LIMIT 200"
    )->fetchAll();
    ?>
    <div class="hint">All contact form submissions. Update status to track follow-ups.</div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>When</th>
            <th>From</th>
            <th>Contact</th>
            <th>Message</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <strong>#<?php echo (int)$r['id']; ?></strong>
                <div class="muted"><?php echo admin_h((string)$r['source']); ?></div>
              </td>
              <td>
                <?php echo admin_h((string)$r['created_at']); ?>
                <div class="muted"><?php echo admin_h((string)($r['page'] ?? '')); ?></div>
              </td>
              <td><?php echo admin_h((string)$r['name']); ?></td>
              <td>
                <div><a href="mailto:<?php echo admin_h((string)$r['email']); ?>"><?php echo admin_h((string)$r['email']); ?></a></div>
                <?php if (!empty($r['phone'])): ?><div class="muted"><?php echo admin_h((string)$r['phone']); ?></div><?php endif; ?>
              </td>
              <td style="white-space:pre-wrap;max-width:520px"><?php echo admin_h((string)($r['message'] ?? '')); ?></td>
              <td><span class="badge"><?php echo admin_h((string)$r['status']); ?></span></td>
              <td>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                  <form method="post" style="margin:0;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                    <input type="hidden" name="action" value="status" />
                    <select name="status" style="min-width:160px">
                      <?php foreach (['new','in_progress','closed','spam'] as $s): ?>
                        <option value="<?php echo admin_h($s); ?>" <?php echo ($r['status'] === $s) ? 'selected' : ''; ?>><?php echo admin_h($s); ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit">Update</button>
                  </form>
                  <form method="post" style="margin:0" onsubmit="return confirm('Delete enquiry?');">
                    <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                    <input type="hidden" name="action" value="delete" />
                    <button class="btn" type="submit">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
});

