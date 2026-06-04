document.addEventListener("DOMContentLoaded", () => {
    initNotifications();
    initBackToTop();
    initDeleteConfirmation();
    initMatchDateValidation();
    initLoginValidation();
    initAlertAutoDismiss();
    initThemeToggle();
});

/* ===========================
   NOTIFICATIONS
=========================== */
function initNotifications() {
    const bellBtn = document.getElementById('notif-bell-btn');
    const notifOverlay = document.getElementById('notif-overlay');
    const notifClose = document.getElementById('notif-close');

    if (!bellBtn || !notifOverlay) return;

    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifOverlay.classList.toggle('open');
    });

    notifClose?.addEventListener('click', () => {
        notifOverlay.classList.remove('open');
    });

    notifOverlay.addEventListener('click', (e) => {
        if (!e.target.closest('.notif-panel')) {
            notifOverlay.classList.remove('open');
        }
    });
}

/* ===========================
   BACK TO TOP + THEME BUTTON
=========================== */
function initBackToTop() {
    const backToTopBtn = document.querySelector('.back-to-top');
    const themeBtn = document.getElementById('theme-toggle-btn');
    const footer = document.querySelector('footer');

    if (!backToTopBtn || !themeBtn || !footer) return;

    window.addEventListener('scroll', () => {

        const visible = window.scrollY > 300;

        backToTopBtn.classList.toggle('is-visible', visible);
        themeBtn.classList.toggle('is-visible', visible);

        const footerTop = footer.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        let arrowBottom = 28;
        if (footerTop < windowHeight) arrowBottom = 80;

        backToTopBtn.style.bottom = arrowBottom + 'px';
        themeBtn.style.bottom = (arrowBottom + 60) + 'px';
    });

    backToTopBtn.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/* ===========================
   CONFIRMATION SUPPRESSION
=========================== */
function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('button[value="supprimer"]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            if (!confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est définitive.")) {
                event.preventDefault();
            }
        });
    });
}

/* ===========================
   VALIDATION DATE MATCH
=========================== */
function initMatchDateValidation() {
    const dateInput = document.getElementById('date');
    if (!dateInput) return;

    const today = new Date();
    const formatted = today.toISOString().split("T")[0];

    dateInput.setAttribute('min', formatted);
}

/* ===========================
   VALIDATION MOT DE PASSE
=========================== */
function initLoginValidation() {
    const loginForm = document.querySelector('form[method="POST"]');
    const passwordInput = document.getElementById('password');

    if (!loginForm || !passwordInput) return;

    const messageContainer = document.createElement('p');
    messageContainer.style.textAlign = 'center';
    passwordInput.insertAdjacentElement("afterend", messageContainer);

    passwordInput.addEventListener('input', (event) => {
        const len = event.target.value.length;
        messageContainer.textContent = (len > 0 && len < 4)
            ? '⚠️ Le mot de passe semble très court.'
            : '';
        messageContainer.style.color = 'orange';
    });
}

/* ===========================
   AUTO-DISMISS MESSAGES
=========================== */
function initAlertAutoDismiss() {
    const paragraphs = document.querySelectorAll('p');

    paragraphs.forEach(p => {
        if (p.style.color === 'green' || p.textContent.includes('supprimé')) {
            setTimeout(() => {
                p.style.transition = 'opacity 0.5s ease';
                p.style.opacity = '0';
                setTimeout(() => p.remove(), 500);
            }, 4000);
        }
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
