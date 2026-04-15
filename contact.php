<?php
declare(strict_types=1);
$active = 'contact';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Contact Elite Print." />
    <meta name="theme-color" content="#0a0a0a" />
    <title>Contact | Elite Print</title>
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
          <p class="page-hero__crumb"><a href="index.php">Home</a> / Contact</p>
          <h1 class="page-hero__title">Contact us</h1>
          <p style="max-width: 560px; color: rgba(250, 250, 250, 0.78)">Call, WhatsApp, or send a message.</p>
        </div>
      </section>

      <section class="section section--dark" id="contact">
        <div class="container">
          <p class="section__eyebrow reveal">Get in touch</p>
          <h2 class="section__title reveal">Contact</h2>
          <p class="section__subtitle reveal">
            <a class="btn btn--gold" href="https://wa.me/919898223383?text=Hello%20Elite%20Print%2C%20please%20call%20or%20message%20me%20on%20WhatsApp." target="_blank" rel="noopener noreferrer">Call / WhatsApp Now</a>
          </p>
          <div class="contact-grid">
            <div class="contact-info reveal">
              <div class="contact-info__block">
                <h4>Address</h4>
                <p>Unit 12, Industrial Estate Phase II, Sample City — 400001, India<br /><em>(Editable placeholder address)</em></p>
              </div>
              <div class="contact-info__block">
                <h4>Phone</h4>
                <p><a href="tel:+919898223383">+91 98982 23383</a></p>
              </div>
              <div class="contact-info__block">
                <h4>Email</h4>
                <p><a href="mailto:hello@eliteprint.example">hello@eliteprint.example</a></p>
              </div>
              <div class="map-wrap">
                <iframe
                  title="Elite Print location map"
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.11609823277!2d72.74109995!3d19.0821978!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c6306644edc1%3A0x5ea2c75244116345!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
              </div>
            </div>

            <div class="contact-form-wrap reveal">
              <h3 class="section__title" style="font-size: 1.75rem; margin-bottom: 1rem">Send a message</h3>
              <form id="contact-form" class="contact-form" novalidate>
                <div>
                  <label for="cname">Name</label>
                  <input id="cname" name="name" type="text" autocomplete="name" required placeholder="Your name" />
                </div>
                <div>
                  <label for="cemail">Email</label>
                  <input id="cemail" name="email" type="email" autocomplete="email" required placeholder="you@company.com" />
                </div>
                <div>
                  <label for="cphone">Phone</label>
                  <input id="cphone" name="phone" type="tel" autocomplete="tel" placeholder="+91 …" />
                </div>
                <div>
                  <label for="cmessage">Message</label>
                  <textarea id="cmessage" name="message" placeholder="Pouch type, quantity, timeline…"></textarea>
                </div>
                <button type="submit" class="btn btn--gold">Submit enquiry</button>
                <p class="form-note" style="color: rgba(250, 250, 250, 0.85); font-size: 0.9rem; margin: 0" hidden></p>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php require __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'footer.php'; ?>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

