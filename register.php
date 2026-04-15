<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Create an Elite Print account." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>Create account | Elite Print</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/effects.css" />
  </head>
  <body class="login-page login-page--fx">
    <a href="#register-main" class="skip-link">Skip to content</a>
    <header class="site-header" id="top">
      <div class="container header-inner">
        <a href="index.php" class="logo logo--mark">
          <img class="logo__img" src="assets/images/logo-elite-print.svg" width="48" height="48" alt="" />
          <span class="logo__text">Elite <span>Print</span></span>
        </a>
      </div>
    </header>

    <main id="register-main" class="page-fade">
      <div class="login-page">
        <div class="login-panel">
          <img src="assets/images/logo-elite-print.svg" alt="" width="72" height="72" style="display:block;margin:0 auto 1rem" />
          <h1>Create account</h1>
          <p class="login-panel__sub">Create your Elite Print customer account.</p>
          <form id="register-form" class="login-form" novalidate>
            <div>
              <label for="rname">Name</label>
              <input id="rname" name="name" type="text" autocomplete="name" required placeholder="Your name" />
            </div>
            <div>
              <label for="remail">Email</label>
              <input id="remail" name="email" type="email" autocomplete="email" required placeholder="you@company.com" />
            </div>
            <div>
              <label for="rpass">Password</label>
              <input id="rpass" name="password" type="password" autocomplete="new-password" required placeholder="Min 8 characters" />
            </div>
            <p class="login-form__error" role="alert"></p>
            <button type="submit" class="btn btn--gold">Create account</button>
          </form>
          <p class="link-muted">
            <a href="login.php">Already have an account?</a> · <a href="index.php">Back to home</a>
          </p>
        </div>
      </div>
    </main>

    <footer class="site-footer">
      <div class="container footer-bottom" style="border:none;padding-top:0">
        <p>&copy; 2026 Elite Print. All rights reserved.</p>
      </div>
    </footer>

    <script>
      (function () {
        var f = document.getElementById("register-form");
        if (!f) return;
        f.addEventListener("submit", function (e) {
          e.preventDefault();
          var fd = new FormData(f);
          var payload = {
            name: (fd.get("name") || "").toString().trim(),
            email: (fd.get("email") || "").toString().trim(),
            password: (fd.get("password") || "").toString(),
          };
          var err = f.querySelector(".login-form__error");
          if (err) err.textContent = "";
          fetch("api/auth/register.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (!data || data.ok !== true) {
                if (err) err.textContent = (data && data.error) ? data.error : "Registration failed.";
                return;
              }
              window.location.href = "login.html";
            })
            .catch(function () {
              if (err) err.textContent = "Registration failed.";
            });
        });
      })();
    </script>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

