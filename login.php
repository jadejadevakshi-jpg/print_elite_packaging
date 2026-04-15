<?php
declare(strict_types=1);
$active = '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Sign in to Elite Print." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>Sign in | Elite Print</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/effects.css" />
  </head>
  <body class="login-page login-page--fx">
    <a href="#login-main" class="skip-link">Skip to content</a>
    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'header.php'; ?>

    <main id="login-main" class="page-fade">
      <div class="login-page">
        <div class="login-panel">
          <img src="assets/images/logo-elite-print.svg" alt="" width="72" height="72" style="display:block;margin:0 auto 1rem" />
          <h1>Sign in</h1>
          <p class="login-panel__sub">Sign in or create a new account.</p>
          <form id="login-form" class="login-form" novalidate method="post" action="api/auth/login.php">
            <div>
              <label for="login-email">Email</label>
              <input id="login-email" name="email" type="email" autocomplete="username" required placeholder="you@company.com" />
            </div>
            <div>
              <label for="login-password">Password</label>
              <input id="login-password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" />
            </div>
            <p class="login-form__error" role="alert"></p>
            <button type="submit" class="btn btn--gold">Continue</button>
          </form>
          <p class="link-muted" style="margin-top:12px">
            <a href="register.php">Create account</a> · <a href="index.php">Back to home</a>
          </p>
        </div>
      </div>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

