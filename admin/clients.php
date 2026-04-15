<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    admin_require_login();
    $pdo = db();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        $website = trim((string)($_POST['website'] ?? ''));
        $logo = trim((string)($_POST['logo_url'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($name !== '') {
            $stmt = $pdo->prepare("INSERT INTO clients (name, website, logo_url, sort_order, is_active) VALUES (:n,:w,:l,:s,:a)");
            $stmt->execute([
                ':n' => $name,
                ':w' => $website !== '' ? $website : null,
                ':l' => $logo !== '' ? $logo : null,
                ':s' => $sort,
                ':a' => $active,
            ]);
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $website = trim((string)($_POST['website'] ?? ''));
        $logo = trim((string)($_POST['logo_url'] ?? ''));
        $sort = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($id > 0 && $name !== '') {
            $stmt = $pdo->prepare("UPDATE clients SET name=:n, website=:w, logo_url=:l, sort_order=:s, is_active=:a WHERE id=:id");
            $stmt->execute([
                ':id' => $id,
                ':n' => $name,
                ':w' => $website !== '' ? $website : null,
                ':l' => $logo !== '' ? $logo : null,
                ':s' => $sort,
                ':a' => $active,
            ]);
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM clients WHERE id=:id");
            $stmt->execute([':id' => $id]);
        }
    }

    header('Location: /project/admin/clients.php');
    exit;
}

admin_render('Clients', 'clients', function (PDO $pdo): void {
    $rows = $pdo->query("SELECT * FROM clients ORDER BY sort_order ASC, name ASC")->fetchAll();
    ?>
    <div class="card" style="margin-top:14px">
      <div class="k">Add client</div>
      <form method="post" style="margin-top:10px">
        <input type="hidden" name="action" value="create" />
        <div class="form-row">
          <div class="span2"><label>Name</label><input type="text" name="name" required /></div>
          <div class="span2"><label>Website</label><input type="url" name="website" placeholder="https://..." /></div>
          <div class="span2"><label>Logo URL</label><input type="url" name="logo_url" placeholder="https://..." /></div>
          <div><label>Sort</label><input type="number" name="sort_order" value="0" /></div>
          <div><label>Active</label><select name="is_active"><option value="1" selected>yes</option><option value="0">no</option></select></div>
          <div><button class="btn btn-primary" type="submit">Create</button></div>
        </div>
      </form>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Website</th>
            <th>Logo URL</th>
            <th>Sort</th>
            <th>Active</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo (int)$r['id']; ?></td>
              <td><?php echo admin_h((string)$r['name']); ?></td>
              <td><?php echo admin_h((string)($r['website'] ?? '')); ?></td>
              <td><?php echo admin_h((string)($r['logo_url'] ?? '')); ?></td>
              <td><?php echo (int)$r['sort_order']; ?></td>
              <td><span class="badge"><?php echo ((int)$r['is_active'] === 1) ? 'yes' : 'no'; ?></span></td>
              <td>
                <details>
                  <summary class="btn" style="display:inline-flex;width:auto">Edit</summary>
                  <div class="card" style="margin-top:10px">
                    <form method="post">
                      <input type="hidden" name="action" value="update" />
                      <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                      <div class="form-row">
                        <div class="span2"><label>Name</label><input type="text" name="name" value="<?php echo admin_h((string)$r['name']); ?>" required /></div>
                        <div class="span2"><label>Website</label><input type="url" name="website" value="<?php echo admin_h((string)($r['website'] ?? '')); ?>" /></div>
                        <div class="span2"><label>Logo URL</label><input type="url" name="logo_url" value="<?php echo admin_h((string)($r['logo_url'] ?? '')); ?>" /></div>
                        <div><label>Sort</label><input type="number" name="sort_order" value="<?php echo (int)$r['sort_order']; ?>" /></div>
                        <div>
                          <label>Active</label>
                          <select name="is_active">
                            <option value="1" <?php echo ((int)$r['is_active'] === 1) ? 'selected' : ''; ?>>yes</option>
                            <option value="0" <?php echo ((int)$r['is_active'] !== 1) ? 'selected' : ''; ?>>no</option>
                          </select>
                        </div>
                        <div><button class="btn btn-primary" type="submit">Save</button></div>
                      </div>
                    </form>
                    <form method="post" onsubmit="return confirm('Delete client?');" style="margin-top:10px">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                      <button class="btn" type="submit">Delete</button>
                    </form>
                  </div>
                </details>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php
});

