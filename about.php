<?php
declare(strict_types=1);
$active = 'about';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="About Elite Print." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>About Us | Elite Print</title>
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
          <p class="page-hero__crumb"><a href="index.php">Home</a> / About</p>
          <h1 class="page-hero__title">About Elite Print</h1>
          <p style="max-width: 720px; color: rgba(250, 250, 250, 0.78)">
            We deliver packaging printing with flexible quantities, crisp output, and reliable production support.
          </p>
        </div>
      </section>

      <section class="section section--light">
        <div class="container">
          <p class="section__eyebrow reveal">Our story</p>
          <h2 class="section__title reveal">Packaging that performs</h2>
          <p class="section__subtitle reveal">
            From agriculture to FMCG, we help teams launch products with shelf-ready packaging and dependable timelines.
          </p>
        </div>
      </section>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

