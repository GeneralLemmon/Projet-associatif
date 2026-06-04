
const bellBtn      = document.getElementById('notif-bell-btn');
const notifOverlay = document.getElementById('notif-overlay');
const notifClose   = document.getElementById('notif-close');

if (bellBtn && notifOverlay) {
    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifOverlay.classList.toggle('open');
    });

    notifClose?.addEventListener('click', () => {
        notifOverlay.classList.remove('open');
    });

    // Fermer en cliquant en dehors du panel
    notifOverlay.addEventListener('click', (e) => {
        if (!e.target.closest('.notif-panel')) {
            notifOverlay.classList.remove('open');
        }
    });
}