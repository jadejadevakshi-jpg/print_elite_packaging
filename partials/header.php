<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . '_auth.php';

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$active = $active ?? '';
$user = current_user();
?>
<header class="site-header" id="top">
  <div class="container header-inner">
    <a href="index.php" class="logo logo--mark">
      <img class="logo__img" src="assets/images/logo-elite-print.svg" width="48" height="48" alt="" />
      <span class="logo__text">Elite <span>Print</span></span>
    </a>
    <nav class="nav" aria-label="Primary">
      <button class="nav__toggle" type="button" aria-expanded="false" aria-controls="nav-menu" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
      <ul class="nav__list" id="nav-menu">
        <li><a class="nav__link <?php echo $active === 'home' ? 'nav__link--active' : ''; ?>" href="index.php">Home</a></li>
        <li><a class="nav__link <?php echo $active === 'about' ? 'nav__link--active' : ''; ?>" href="about.php">About</a></li>
        <li><a class="nav__link <?php echo $active === 'products' ? 'nav__link--active' : ''; ?>" href="products.php">Products</a></li>
        <li><a class="nav__link <?php echo $active === 'clients' ? 'nav__link--active' : ''; ?>" href="clients.php">Clients</a></li>
        <li><a class="nav__link <?php echo $active === 'faq' ? 'nav__link--active' : ''; ?>" href="faq.php">FAQ</a></li>
        <li><a class="nav__link <?php echo $active === 'contact' ? 'nav__link--active' : ''; ?>" href="contact.php">Contact</a></li>

        <?php if (!$user): ?>
          <li><a class="nav__link" href="register.php">Create account</a></li>
          <li><a class="nav__link" href="login.php">Sign in</a></li>
        <?php else: ?>
          <?php if (($user['role'] ?? '') === 'admin'): ?>
            <li><a class="nav__link" href="admin/">Admin</a></li>
          <?php else: ?>
            <li><a class="nav__link" href="account.php">My account</a></li>
          <?php endif; ?>
          <li>
            <form method="post" action="logout.php" style="margin:0">
              <button type="submit" class="nav__link" style="background:none;border:none;padding:0;cursor:pointer">Logout</button>
            </form>
          </li>
        <?php endif; ?>

        <li>
          <a class="btn btn--gold" href="https://wa.me/919898223383?text=Hello%20Elite%20Print%2C%20I%20would%20like%20to%20enquire." target="_blank" rel="noopener noreferrer">Enquire Now</a>
        </li>
      </ul>
    </nav>
  </div>
</header>

