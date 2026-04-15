<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_bootstrap.php';

function admin_h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function admin_render(string $title, string $activeKey, callable $body): void
{
    $adminId = admin_require_login();
    $pdo = db();
    $newEnq = (int)$pdo->query("SELECT COUNT(*) c FROM enquiries WHERE status='new'")->fetch()['c'];

    $nav = [
        'dashboard' => ['label' => 'Dashboard', 'href' => '/project/admin/'],
        'orders' => ['label' => 'Orders', 'href' => '/project/admin/orders.php'],
        'payments' => ['label' => 'Payments', 'href' => '/project/admin/payments.php'],
        'enquiries' => ['label' => 'Enquiries', 'href' => '/project/admin/enquiries.php', 'badge' => $newEnq],
        'clients' => ['label' => 'Clients', 'href' => '/project/admin/clients.php'],
        'categories' => ['label' => 'Categories', 'href' => '/project/admin/categories.php'],
        'products' => ['label' => 'Products', 'href' => '/project/admin/products.php'],
        'setup' => ['label' => 'Setup', 'href' => '/project/admin/setup.php'],
    ];
    ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo admin_h($title); ?> | Elite Print Admin</title>
    <link rel="stylesheet" href="/project/assets/css/admin.css" />
  </head>
  <body>
    <div class="admin-shell">
      <aside class="admin-side">
        <div class="admin-brand">
          <img src="/project/assets/images/logo-elite-print.svg" alt="" />
          <div>
            <div class="t">Elite <span>Print</span></div>
            <div class="admin-sub">Admin console</div>
          </div>
        </div>
        <nav class="admin-nav" aria-label="Admin navigation">
          <?php foreach ($nav as $key => $item): ?>
            <a class="admin-link <?php echo $activeKey === $key ? 'is-active' : ''; ?>" href="<?php echo admin_h($item['href']); ?>">
              <span><?php echo admin_h($item['label']); ?></span>
              <?php if (isset($item['badge']) && (int)$item['badge'] > 0): ?>
                <span class="badge badge-warn"><?php echo (int)$item['badge']; ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </nav>
      </aside>

      <main class="admin-main">
        <div class="admin-topbar">
          <h1 class="admin-title"><?php echo admin_h($title); ?></h1>
          <div class="admin-actions">
            <span class="pill">Signed in</span>
            <form method="post" action="/project/admin/logout.php" style="margin:0">
              <button class="btn" type="submit">Logout</button>
            </form>
            <a class="btn btn-primary" href="/project/index.php">View site</a>
          </div>
        </div>

        <?php $body($pdo, $adminId); ?>
      </main>
    </div>
  </body>
</html>
<?php
}

