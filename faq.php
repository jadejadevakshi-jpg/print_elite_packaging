<?php
declare(strict_types=1);

$active = 'faq';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '_bootstrap.php';
$pdo = db();
$faqs = $pdo->query(
    "SELECT question, answer
     FROM faqs
     WHERE is_active = 1
     ORDER BY sort_order ASC, id ASC"
)->fetchAll();

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="FAQ." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>FAQ | Elite Print</title>
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
          <p class="page-hero__crumb"><a href="index.php">Home</a> / FAQ</p>
          <h1 class="page-hero__title">Frequently asked questions</h1>
          <p style="max-width: 560px; color: rgba(250, 250, 250, 0.78)">Answers pulled from the database.</p>
        </div>
      </section>

      <section class="section section--light" id="faq">
        <div class="container">
          <p class="section__eyebrow reveal">Support</p>
          <h2 class="section__title reveal">Answers</h2>
          <p class="section__subtitle reveal">Manage FAQs in the database.</p>
          <div class="faq-list">
            <?php foreach ($faqs as $f): ?>
              <div class="faq-item reveal">
                <button type="button" class="faq-trigger">
                  <?php echo h((string)$f['question']); ?>
                  <span class="faq-icon" aria-hidden="true"></span>
                </button>
                <div class="faq-panel">
                  <p><?php echo h((string)$f['answer']); ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

