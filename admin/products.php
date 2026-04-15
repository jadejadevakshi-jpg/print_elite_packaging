<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_layout.php';
admin_require_login();
$pdo = db();
$cats = $pdo->query("SELECT id, name FROM product_categories ORDER BY sort_order ASC, name ASC")->fetchAll();
$catById = [];
foreach ($cats as $c) $catById[(int)$c['id']] = (string)$c['name'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'create') {
        $cid = (int)($_POST['category_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $desc = trim((string)($_POST['description'] ?? ''));
        $img = trim((string)($_POST['image_url'] ?? ''));
        $sku = trim((string)($_POST['sku'] ?? ''));
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($cid > 0 && $name !== '') {
            $stmt = $pdo->prepare("INSERT INTO product_items (category_id, name, description, image_url, sku, is_active) VALUES (:c,:n,:d,:i,:s,:a)");
            $stmt->execute([
                ':c' => $cid,
                ':n' => $name,
                ':d' => $desc !== '' ? $desc : null,
                ':i' => $img !== '' ? $img : null,
                ':s' => $sku !== '' ? $sku : null,
                ':a' => $active,
            ]);
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $cid = (int)($_POST['category_id'] ?? 0);
        $name = trim((string)($_POST['name'] ?? ''));
        $desc = trim((string)($_POST['description'] ?? ''));
        $img = trim((string)($_POST['image_url'] ?? ''));
        $sku = trim((string)($_POST['sku'] ?? ''));
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($id > 0 && $cid > 0 && $name !== '') {
            $stmt = $pdo->prepare("UPDATE product_items SET category_id=:c, name=:n, description=:d, image_url=:i, sku=:s, is_active=:a WHERE id=:id");
            $stmt->execute([
                ':id' => $id,
                ':c' => $cid,
                ':n' => $name,
                ':d' => $desc !== '' ? $desc : null,
                ':i' => $img !== '' ? $img : null,
                ':s' => $sku !== '' ? $sku : null,
                ':a' => $active,
            ]);
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM product_items WHERE id=:id");
            $stmt->execute([':id' => $id]);
        }
    }

    header('Location: /project/admin/products.php');
    exit;
}

$rows = $pdo->query(
    "SELECT id, category_id, name, description, image_url, sku, is_active, created_at
     FROM product_items
     ORDER BY id DESC
     LIMIT 300"
)->fetchAll();

admin_render('Products', 'products', function (PDO $pdo) use ($cats, $catById, $rows): void {
    ?>
    <div class="card" style="margin-top:14px">
      <div class="k">Add product</div>
      <form method="post" style="margin-top:10px">
        <input type="hidden" name="action" value="create" />
        <div class="form-row">
          <div class="span2">
            <label>Category</label>
            <select name="category_id" required>
              <option value="">Select...</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>"><?php echo admin_h((string)$c['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="span2"><label>Name</label><input type="text" name="name" required /></div>
          <div class="span3"><label>Description</label><textarea name="description"></textarea></div>
          <div class="span2"><label>Image URL</label><input type="url" name="image_url" placeholder="https://..." /></div>
          <div><label>SKU</label><input type="text" name="sku" placeholder="optional" /></div>
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
            <th>Category</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image URL</th>
            <th>SKU</th>
            <th>Active</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo (int)$r['id']; ?></td>
              <td><span class="badge"><?php echo admin_h($catById[(int)$r['category_id']] ?? ('#' . (int)$r['category_id'])); ?></span></td>
              <td><?php echo admin_h((string)$r['name']); ?></td>
              <td><?php echo admin_h((string)($r['description'] ?? '')); ?></td>
              <td><?php echo admin_h((string)($r['image_url'] ?? '')); ?></td>
              <td><?php echo admin_h((string)($r['sku'] ?? '')); ?></td>
              <td><span class="badge"><?php echo ((int)$r['is_active'] === 1) ? 'yes' : 'no'; ?></span></td>
              <td>
                <details>
                  <summary class="btn" style="display:inline-flex;width:auto">Edit</summary>
                  <div class="card" style="margin-top:10px">
                    <form method="post">
                      <input type="hidden" name="action" value="update" />
                      <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>" />
                      <div class="form-row">
                        <div class="span2">
                          <label>Category</label>
                          <select name="category_id" required>
                            <?php foreach ($cats as $c): ?>
                              <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)$r['category_id'] === (int)$c['id']) ? 'selected' : ''; ?>>
                                <?php echo admin_h((string)$c['name']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="span2"><label>Name</label><input type="text" name="name" value="<?php echo admin_h((string)$r['name']); ?>" required /></div>
                        <div class="span3"><label>Description</label><textarea name="description"><?php echo admin_h((string)($r['description'] ?? '')); ?></textarea></div>
                        <div class="span2"><label>Image URL</label><input type="url" name="image_url" value="<?php echo admin_h((string)($r['image_url'] ?? '')); ?>" /></div>
                        <div><label>SKU</label><input type="text" name="sku" value="<?php echo admin_h((string)($r['sku'] ?? '')); ?>" /></div>
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
                    <form method="post" onsubmit="return confirm('Delete product?');" style="margin-top:10px">
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

