<?php
declare(strict_types=1);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . '_auth.php';
$u = require_customer();

function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Place order | Elite Print</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/effects.css" />
    <style>
      .card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px}
      .grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
      .grid3{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px}
      input,textarea{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.22);color:#fff}
      textarea{min-height:90px;resize:vertical}
      .muted{color:rgba(250,250,250,.7);font-size:.9rem}
      .items{display:flex;flex-direction:column;gap:10px;margin-top:10px}
      .row{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:end}
      .xbtn{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;border-radius:12px;padding:10px 12px;cursor:pointer}
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
            <li><a class="nav__link" href="account.php">← My account</a></li>
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
          <p class="page-hero__crumb"><a href="account.php">Account</a> / New order</p>
          <h1 class="page-hero__title">Place a new order</h1>
          <p style="max-width:720px;color:rgba(250,250,250,.78)">Add products and quantities. Admin will move it through processing/printing/dispatch.</p>
        </div>
      </section>

      <section class="section section--dark">
        <div class="container">
          <div class="card">
            <div class="muted">Customer: <?php echo h((string)$u['email']); ?></div>
            <div class="items" id="items"></div>
            <p style="margin:10px 0 0">
              <button class="xbtn" type="button" id="addItem">+ Add item</button>
            </p>
            <div style="margin-top:14px">
              <label class="muted" for="notes">Notes (optional)</label>
              <textarea id="notes" placeholder="Sizes, material, finishes, timeline..."></textarea>
            </div>
            <p class="muted" id="err" style="margin:10px 0 0"></p>
            <p style="margin:12px 0 0">
              <button class="btn btn--gold" type="button" id="submitOrder">Submit order</button>
            </p>
          </div>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container footer-bottom">
        <p>&copy; 2026 Elite Print. All rights reserved.</p>
      </div>
    </footer>

    <script>
      (function () {
        var itemsEl = document.getElementById("items");
        var addBtn = document.getElementById("addItem");
        var submitBtn = document.getElementById("submitOrder");
        var notesEl = document.getElementById("notes");
        var errEl = document.getElementById("err");

        function addRow(prefill) {
          var row = document.createElement("div");
          row.className = "row";
          row.innerHTML =
            '<div><label class="muted">Product name</label><input type="text" name="product_name" placeholder="e.g. Stand-up pouch" /></div>' +
            '<div><label class="muted">Qty</label><input type="number" name="quantity" min="1" value="1" /></div>' +
            '<div><label class="muted">Unit price (optional)</label><input type="number" name="unit_price" min="0" step="0.01" value="0" /></div>' +
            '<div><button type="button" class="xbtn" aria-label="Remove">Remove</button></div>';
          row.querySelector("button").addEventListener("click", function () {
            row.remove();
          });
          if (prefill) {
            row.querySelector('[name="product_name"]').value = prefill.product_name || "";
            row.querySelector('[name="quantity"]').value = prefill.quantity || 1;
            row.querySelector('[name="unit_price"]').value = prefill.unit_price || 0;
          }
          itemsEl.appendChild(row);
        }

        function collect() {
          var rows = Array.prototype.slice.call(itemsEl.querySelectorAll(".row"));
          return rows.map(function (r) {
            return {
              product_name: (r.querySelector('[name="product_name"]').value || "").trim(),
              quantity: parseInt(r.querySelector('[name="quantity"]').value || "0", 10),
              unit_price: parseFloat(r.querySelector('[name="unit_price"]').value || "0"),
            };
          });
        }

        addBtn.addEventListener("click", function () { addRow(); });
        addRow({ product_name: "Pouch Printing", quantity: 1, unit_price: 0 });

        submitBtn.addEventListener("click", function () {
          if (errEl) errEl.textContent = "";
          var payload = { items: collect(), notes: (notesEl.value || "").trim() };
          fetch("api/orders/create.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          })
            .then(function (r) { return r.json(); })
            .then(function (data) {
              if (!data || data.ok !== true) {
                if (errEl) errEl.textContent = (data && data.error) ? data.error : "Could not place order.";
                return;
              }
              window.location.href = "account.php";
            })
            .catch(function () {
              if (errEl) errEl.textContent = "Could not place order.";
            });
        });
      })();
    </script>
    <script src="assets/js/main.js" defer></script>
  </body>
</html>

