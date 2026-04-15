<?php
declare(strict_types=1);
$active = 'home';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Elite Print — without cylinder pouch printing, mono cartons, labels, HDPE and BOPP bags." />
    <meta name="keywords" content="pouch printing, flexible packaging, mono carton, BOPP bags, HDPE bags, labels" />
    <meta name="theme-color" content="#0a0a0a" />
    <title>Elite Print</title>
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
          <p class="page-hero__crumb">Home</p>
          <h1 class="page-hero__title">Digital packaging printing</h1>
          <p style="max-width: 720px; color: rgba(250, 250, 250, 0.78)">
            Without-cylinder pouch printing, mono cartons, labels, HDPE and BOPP bags — fast turnaround, consistent colour, premium finishes.
          </p>
          <p style="margin-top: 16px">
            <a class="btn btn--gold" href="products.php">Browse products</a>
            <a class="btn btn--outline-dark" style="margin-left: 10px; border-color: rgba(255,255,255,.22); color:#fff" href="contact.php">Contact</a>
          </p>
        </div>
      </section>

      <section class="section section--light">
        <div class="container">
          <p class="section__eyebrow reveal">Why Elite Print</p>
          <h2 class="section__title reveal">Quality, speed, flexibility</h2>
          <p class="section__subtitle reveal">Place an order from your account and track processing, printing, dispatch, and delivery.</p>
          <div class="features-grid">
            <article class="feature-card reveal"><h3>Low MOQ</h3><p>Smaller runs without heavy cylinder costs.</p></article>
            <article class="feature-card reveal"><h3>Sharp artwork</h3><p>Crisp digital output with consistent colour.</p></article>
            <article class="feature-card reveal"><h3>Fast timelines</h3><p>Reliable production planning and updates.</p></article>
          </div>
        </div>
      </section>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>

    <script src="assets/js/main.js" defer></script>
  </body>
</html>

