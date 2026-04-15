<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . '_auth.php';
$u = require_customer();

$pdo = db();
$orders = $pdo->prepare(
    "SELECT id, order_no, status, total_amount, currency, created_at
     FROM orders
     WHERE customer_id = :cid
     ORDER BY created_at DESC
     LIMIT 50"
);
$orders->execute([':cid' => (int)$u['id']]);
$rows = $orders->fetchAll();

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My account | Elite Print</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/effects.css" />
    <style>
      .card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px}
      table{width:100%;border-collapse:collapse;margin-top:12px}
      th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left;vertical-align:top}
      th{font-size:12px;opacity:.8}
      .tag{display:inline-block;padding:2px 10px;border-radius:999px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);font-size:12px}
    </style>
  </head>
  <body class="has-mesh">
    <header class="site-header" id="top">
      <div class="container header-inner">
        <a href="index.php" class="logo logo--mark">
          <img class="logo__img" src="assets/images/logo-elite-print.svg" width="48" height="48" alt="" />
          <span class="logo__text">Elite <span>Print</span></span>
        </a>
        <nav class="nav" aria-label="Primary">
          <ul class="nav__list" style="display:flex;gap:12px;align-items:center">
            <li><a class="nav__link" href="index.php">Home</a></li>
            <li><a class="nav__link" href="products.php">Products</a></li>
            <li><a class="nav__link nav__link--active" href="account.php">My account</a></li>
            <li>
              <form method="post" action="logout.php" style="margin:0">
                <button type="submit" class="btn btn--outline-dark" style="border-color:rgba(255,255,255,.2);color:#fff">Logout</button>
              </form>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <main id="main-content" class="page-fade">
      <section class="page-hero section--dark">
        <div class="container">
          <p class="page-hero__crumb"><a href="index.php">Home</a> / Account</p>
          <h1 class="page-hero__title">My account</h1>
          <p style="max-width:620px;color:rgba(250,250,250,.78)">
            Signed in as <strong><?php echo h((string)$u['email']); ?></strong>
          </p>
          <p style="margin-top:14px">
            <a class="btn btn--gold" href="order_new.php">Place new order</a>
          </p>
        </div>
      </section>

      <section class="section section--dark">
        <div class="container">
          <div class="card">
            <h2 class="section__title" style="font-size:1.6rem;margin:0 0 6px">My orders</h2>
            <p class="section__subtitle" style="margin:0 0 12px">Track processing, printing, dispatch, and delivery.</p>
            <?php if (!$rows): ?>
              <p style="margin:0;color:rgba(250,250,250,.8)">No orders yet.</p>
            <?php else: ?>
              <table>
                <thead>
                  <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Created</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($rows as $o): ?>
                    <tr>
                      <td><?php echo h((string)$o['order_no']); ?></td>
                      <td><span class="tag"><?php echo h((string)$o['status']); ?></span></td>
                      <td><?php echo h((string)$o['currency']); ?> <?php echo number_format((float)$o['total_amount'], 2); ?></td>
                      <td><?php echo h((string)$o['created_at']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container footer-bottom">
        <p>&copy; 2026 Elite Print. All rights reserved.</p>
      </div>
    </footer>

    <script src="assets/js/main.js" defer></script>
  </body>
</html>

