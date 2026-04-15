<?php
declare(strict_types=1);

$active = 'products';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '_bootstrap.php';

$pdo = db();
$cats = $pdo->query(
    "SELECT id, name, description, image_url
     FROM product_categories
     WHERE is_active = 1
     ORDER BY sort_order ASC, name ASC"
)->fetchAll();

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Products & pouch formats." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>Products | Elite Print</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/effects.css" />
  </head>
  <body class="has-mesh">
    <div class="logo-splash" id="logo-splash" aria-hidden="false">
      <div class="logo-splash__glow" aria-hidden="true"></div>
      <div class="logo-splash__content">
        <img src="assets/images/logo-elite-print.svg" alt="" width="160" height="160" />
        <p class="logo-splash__name">Elite Print</p>
      </div>
    </div>
    <a href="#main-content" class="skip-link">Skip to content</a>
    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <main id="main-content" class="page-fade">
      <section class="page-hero section--dark">
        <div class="container">
          <p class="page-hero__crumb"><a href="index.php">Home</a> / Products</p>
          <h1 class="page-hero__title">Products & pouch formats</h1>
          <p style="max-width: 560px; color: rgba(250, 250, 250, 0.78)">Browse categories powered from the database.</p>
        </div>
      </section>

      <section class="section section--light" id="products">
        <div class="container">
          <p class="section__eyebrow reveal">What we offer</p>
          <h2 class="section__title reveal">Product categories</h2>
          <p class="section__subtitle reveal">Manage categories in Admin → Categories.</p>
          <div class="products-grid">
            <?php foreach ($cats as $c): ?>
              <article class="product-card product-card--fx reveal">
                <?php if (!empty($c['image_url'])): ?>
                  <img class="product-card__img" src="<?php echo h((string)$c['image_url']); ?>" alt="<?php echo h((string)$c['name']); ?>" width="800" height="600" loading="lazy" />
                <?php endif; ?>
                <div class="product-card__body">
                  <h3 class="product-card__title"><?php echo h((string)$c['name']); ?></h3>
                  <p class="product-card__text"><?php echo h((string)($c['description'] ?? '')); ?></p>
                  <a class="btn btn--outline-dark" href="https://wa.me/919898223383?text=<?php echo rawurlencode('Enquiry: ' . (string)$c['name'] . ' (Elite Print)'); ?>" target="_blank" rel="noopener noreferrer">Get quote</a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

