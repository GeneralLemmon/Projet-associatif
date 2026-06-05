document.addEventListener("DOMContentLoaded", () => {
  initNotifications();
  initBackToTop();
  initDeleteConfirmation();
  initMatchDateValidation();
  initLoginValidation();
  initAlertAutoDismiss();
  initThemeToggle();
  initLevelHelp();
  initSearchPage();
  initManagePage();
  initPlayersPage();
});

/* ===========================
   NOTIFICATIONS
=========================== */
function initNotifications() {
  const bellBtn = document.getElementById("notif-bell-btn");
  const notifOverlay = document.getElementById("notif-overlay");
  const notifClose = document.getElementById("notif-close");

  if (!bellBtn || !notifOverlay) return;

  bellBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    notifOverlay.classList.toggle("open");
  });

  notifClose?.addEventListener("click", () => {
    notifOverlay.classList.remove("open");
  });

  notifOverlay.addEventListener("click", (e) => {
    if (!e.target.closest(".notif-panel")) {
      notifOverlay.classList.remove("open");
    }
  });
}

/* ===========================
   LEVEL HELP OVERLAY
=========================== */
function initLevelHelp() {
  const overlay = document.getElementById("level-overlay");
  const closeBtn = document.getElementById("level-close");

  if (!overlay) return;

  document.addEventListener("click", (e) => {
    if (
      e.target.classList.contains("help-icon") &&
      !e.target.closest("#level-overlay")
    ) {
      e.stopPropagation();
      overlay.classList.add("open");
    }
  });

  closeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    overlay.classList.remove("open");
  });

  overlay.addEventListener("click", (e) => {
    if (!e.target.closest(".notif-panel")) {
      overlay.classList.remove("open");
    }
  });
}

/* ===========================
   BACK TO TOP + THEME BUTTON
=========================== */
function initBackToTop() {
  const backToTopBtn = document.querySelector(".back-to-top");
  const themeBtn = document.getElementById("theme-toggle-btn");
  const footer = document.querySelector("footer");

  if (!backToTopBtn || !themeBtn || !footer) return;

  window.addEventListener("scroll", () => {
    const visible = window.scrollY > 300;

    backToTopBtn.classList.toggle("is-visible", visible);
    themeBtn.classList.toggle("is-visible", visible);

    const footerTop = footer.getBoundingClientRect().top;
    const windowHeight = window.innerHeight;

    let arrowBottom = 28;
    if (footerTop < windowHeight) arrowBottom = 80;

    backToTopBtn.style.bottom = arrowBottom + "px";
    themeBtn.style.bottom = arrowBottom + 60 + "px";
  });

  backToTopBtn.addEventListener("click", (event) => {
    event.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

/* ===========================
   CONFIRMATION SUPPRESSION (manage.php)
=========================== */
function initDeleteConfirmation() {
  document.querySelectorAll('button[value="supprimer"]').forEach((button) => {
    button.addEventListener("click", (event) => {
      if (
        !confirm(
          "Êtes-vous sûr de vouloir supprimer ce match ? Cette action est définitive.",
        )
      ) {
        event.preventDefault();
      }
    });
  });
}

/* ===========================
   VALIDATION DATE MATCH
=========================== */
function initMatchDateValidation() {
  const dateInput = document.getElementById("date");
  if (!dateInput) return;

  const today = new Date();
  const formatted = today.toISOString().split("T")[0];
  dateInput.setAttribute("min", formatted);
}

/* ===========================
   VALIDATION MOT DE PASSE
=========================== */
function initLoginValidation() {
  const loginForm = document.querySelector('form[method="POST"]');
  const passwordInput = document.getElementById("password");

  if (!loginForm || !passwordInput) return;

  const messageContainer = document.createElement("p");
  messageContainer.style.textAlign = "center";
  passwordInput.insertAdjacentElement("afterend", messageContainer);

  passwordInput.addEventListener("input", (event) => {
    const len = event.target.value.length;
    messageContainer.textContent =
      len > 0 && len < 4 ? "⚠️ Le mot de passe semble très court." : "";
    messageContainer.style.color = "orange";
  });
}

/* ===========================
   AUTO-DISMISS MESSAGES (2,5s)
=========================== */
function initAlertAutoDismiss() {
  const autoDismissItems = document.querySelectorAll('.auto-dismiss');

  autoDismissItems.forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.5s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 4000);
  });
}

/* ===========================
   DARK / LIGHT MODE
=========================== */
function initThemeToggle() {
  const toggleBtn = document.getElementById("theme-toggle-btn");
  const icon = document.getElementById("theme-icon");

  if (!toggleBtn || !icon) return;

  const lightIcon = "./Images/sun.png";
  const darkIcon = "./Images/moon.png";

  if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-mode");
    icon.src = lightIcon;
  }

  toggleBtn.addEventListener("click", () => {
    const isDark = document.body.classList.toggle("dark-mode");
    icon.src = isDark ? lightIcon : darkIcon;
    localStorage.setItem("theme", isDark ? "dark" : "light");
  });
}

/* ===========================
   SEARCH PAGE
=========================== */
function initSearchPage() {
  const cardModal = document.getElementById("card-action-modal");
  if (!cardModal) return;

  const modalClose = document.getElementById("modal-close");
  const modalCancel = document.getElementById("modal-cancel");
  const modalPlayersButton = document.getElementById("modal-players-button");
  const modalMapButton = document.getElementById("modal-map-button");

  document.querySelectorAll(".match-card").forEach((card) => {
    card.addEventListener("click", (event) => {
      if (
        event.target.closest("form") ||
        event.target.closest("a") ||
        event.target.closest("button")
      ) {
        return;
      }
      modalPlayersButton.dataset.timeslot = card.dataset.timeslot;
      modalMapButton.dataset.location = card.dataset.location || "Lieu inconnu";
      cardModal.classList.add("open");
    });
  });

  function closeCardModal() {
    cardModal.classList.remove("open");
  }

  [modalClose, modalCancel].forEach((btn) =>
    btn.addEventListener("click", closeCardModal),
  );

  cardModal.addEventListener("click", (event) => {
    if (event.target === cardModal) closeCardModal();
  });

  modalPlayersButton.addEventListener("click", () => {
    const id = modalPlayersButton.dataset.timeslot;
    if (id) window.location.href = `players.php?id=${encodeURIComponent(id)}`;
  });

  modalMapButton.addEventListener("click", () => {
    const location = modalMapButton.dataset.location;
    if (location)
      window.open(
        `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`,
        "_blank",
      );
  });

  document.querySelectorAll("form.join-form").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (window.confirm("Confirmer votre participation à ce match ?")) {
        form.submit();
      }
    });
  });
}

/* ===========================
   MANAGE PAGE
=========================== */
function initManagePage() {
  const cardModal = document.getElementById("card-action-modal");
  if (!cardModal) return;

  // Sur search.php le modal existe aussi, on différencie via un bouton spécifique à manage
  const isManagePage = !!document.querySelector('button[value="supprimer"]');
  if (!isManagePage) return;

  const modalClose = document.getElementById("modal-close");
  const modalCancel = document.getElementById("modal-cancel");
  const modalPlayersButton = document.getElementById("modal-players-button");
  const modalMapButton = document.getElementById("modal-map-button");

  document.querySelectorAll(".match-card").forEach((card) => {
    card.addEventListener("click", (event) => {
      if (
        event.target.closest("form") ||
        event.target.closest("a") ||
        event.target.closest("button")
      ) {
        return;
      }
      modalPlayersButton.dataset.timeslot = card.dataset.timeslot;
      modalMapButton.dataset.location = card.dataset.location || "Lieu inconnu";
      cardModal.classList.add("open");
    });
  });

  function closeCardModal() {
    cardModal.classList.remove("open");
  }

  [modalClose, modalCancel].forEach((btn) =>
    btn.addEventListener("click", closeCardModal),
  );

  cardModal.addEventListener("click", (event) => {
    if (event.target === cardModal) closeCardModal();
  });

  modalPlayersButton.addEventListener("click", () => {
    const id = modalPlayersButton.dataset.timeslot;
    if (id) window.location.href = `players.php?id=${encodeURIComponent(id)}`;
  });

  modalMapButton.addEventListener("click", () => {
    const location = modalMapButton.dataset.location;
    if (location)
      window.open(
        `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`,
        "_blank",
      );
  });
}

/* ===========================
   PLAYERS PAGE
=========================== */
function initPlayersPage() {
  document.querySelectorAll("form").forEach((form) => {
    if (!form.querySelector('input[name="remove_user_id"]')) return;

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      if (
        window.confirm(
          "Êtes-vous sûr de vouloir supprimer ce joueur du match ?",
        )
      ) {
        form.submit();
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".plage-btn");
  const exactTimeSelect = document.getElementById("exact_time");
  const hiddenInput = document.getElementById("heure");

  const hoursBySlot = {
    matin: ["07:00", "08:00", "09:00"],
    midi: ["12:00", "13:00", "14:00"],
    soir: ["18:00", "19:00", "20:00", "21:00", "22:00"],
  };

  function updateExactHours(selectedSlot) {
    const allowedHours = hoursBySlot[selectedSlot] || [];
    const options = exactTimeSelect.options;
    let firstAvailableSet = false;

    for (let i = 0; i < options.length; i++) {
      const optionValue = options[i].value;

      if (optionValue === "any") {
        options[i].style.display = "block";
        options[i].disabled = false;
        continue;
      }

      if (allowedHours.includes(optionValue)) {
        options[i].style.display = "block";
        options[i].disabled = false;

        if (!firstAvailableSet) {
          exactTimeSelect.value = optionValue;
          if (hiddenInput) hiddenInput.value = optionValue;
          firstAvailableSet = true;
        }
      } else {
        options[i].style.display = "none";
        options[i].disabled = true;
      }
    }
  }

  buttons.forEach(function (button) {
    button.addEventListener("click", function () {
      // Gestion visuelle de la classe 'active'
      buttons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      const chosenSlot = this.getAttribute("data-plage");

      // Mise à jour du select
      updateExactHours(chosenSlot);
    });
  });

  const initialActiveButton = document.querySelector(".plage-btn.active");
  if (initialActiveButton) {
    updateExactHours(initialActiveButton.getAttribute("data-plage"));
  }
});
