(function () {
  "use strict";

  var header = document.querySelector(".site-header");
  var navToggle = document.querySelector(".nav__toggle");
  var navList = document.querySelector(".nav__list");
  var waBase = "https://wa.me/919898223383?text=";
  var splash = document.getElementById("logo-splash");
  var splashDuration = 3200;

  function initSplash() {
    if (!splash) return;
    document.body.classList.add("splash-lock");
    var finished = false;
    function unlock() {
      if (finished) return;
      finished = true;
      document.body.classList.remove("splash-lock");
      splash.setAttribute("aria-hidden", "true");
      splash.setAttribute("inert", "");
      try {
        splash.remove();
      } catch (e) {
        splash.style.display = "none";
      }
    }
    splash.addEventListener(
      "animationend",
      function (ev) {
        if (ev.target !== splash) return;
        if (ev.animationName === "splashSequence" || ev.animationName === "splashReduced") {
          unlock();
        }
      },
      false
    );
    setTimeout(unlock, splashDuration + 500);
  }

  function onScroll() {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 40);
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();
  initSplash();

  if (navToggle && navList) {
    navToggle.addEventListener("click", function () {
      var open = navList.classList.toggle("is-open");
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
    });

    navList.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        navList.classList.remove("is-open");
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  document.querySelectorAll(".faq-item").forEach(function (item) {
    var trigger = item.querySelector(".faq-trigger");
    var panel = item.querySelector(".faq-panel");
    if (!trigger || !panel) return;

    trigger.addEventListener("click", function () {
      var isOpen = item.classList.contains("is-open");
      document.querySelectorAll(".faq-item.is-open").forEach(function (other) {
        if (other !== item) {
          other.classList.remove("is-open");
          var p = other.querySelector(".faq-panel");
          if (p) p.style.maxHeight = "0";
        }
      });
      if (isOpen) {
        item.classList.remove("is-open");
        panel.style.maxHeight = "0";
      } else {
        item.classList.add("is-open");
        panel.style.maxHeight = panel.scrollHeight + "px";
      }
    });
  });

  var form = document.querySelector("#contact-form");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var fd = new FormData(form);
      var name = (fd.get("name") || "").toString().trim();
      var email = (fd.get("email") || "").toString().trim();
      var phone = (fd.get("phone") || "").toString().trim();
      var message = (fd.get("message") || "").toString().trim();
      // Save enquiry to the database (PHP API), then also open WhatsApp as fallback.
      try {
        fetch("api/contact/create.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            name: name,
            email: email,
            phone: phone,
            message: message,
            page: window.location.pathname.split("/").pop() || "contact.html",
          }),
        }).catch(function () {});
      } catch (e2) {}
      var text =
        "Contact form — Elite Print\nName: " +
        name +
        "\nEmail: " +
        email +
        "\nPhone: " +
        phone +
        "\nMessage: " +
        message;
      window.open(waBase + encodeURIComponent(text), "_blank", "noopener,noreferrer");
      form.reset();
      var note = form.querySelector(".form-note");
      if (note) {
        note.textContent = "Thank you. Your enquiry was sent via WhatsApp.";
        note.hidden = false;
        setTimeout(function () {
          note.hidden = true;
        }, 5000);
      }
    });
  }

  var loginForm = document.getElementById("login-form");
  if (loginForm) {
    if (loginForm.dataset && loginForm.dataset.server === "1") {
      // When using the Python backend, allow normal form submission.
      // (No preventDefault; the server will validate and redirect.)
    } else {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();
      var emailEl = loginForm.querySelector('[name="email"]');
      var passEl = loginForm.querySelector('[name="password"]');
      var err = loginForm.querySelector(".login-form__error");
      var email = emailEl ? emailEl.value.trim() : "";
      var password = passEl ? passEl.value : "";
      if (!email || password.length < 8) {
        if (err) err.textContent = "Use a valid email and a password of at least 8 characters.";
        return;
      }
      if (err) err.textContent = "";
      fetch("api/auth/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: email, password: password }),
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (!data || data.ok !== true) {
            if (err) err.textContent = (data && data.error) ? data.error : "Login failed.";
            return;
          }
          if (data.user && data.user.role === "admin") {
            window.location.href = "admin/";
          } else {
            window.location.href = "account.php";
          }
        })
        .catch(function () {
          if (err) err.textContent = "Login failed.";
        });
    });
    }
  }

  if ("IntersectionObserver" in window) {
    var revealEls = document.querySelectorAll(".reveal");
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { rootMargin: "0px 0px -8% 0px", threshold: 0.08 }
    );
    revealEls.forEach(function (el) {
      io.observe(el);
    });
  } else {
    document.querySelectorAll(".reveal").forEach(function (el) {
      el.classList.add("is-visible");
    });
  }

})();
