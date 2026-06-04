document.addEventListener('DOMContentLoaded', () => {
    initBackToTop();
    initDeleteConfirmation();
    initMatchDateValidation();
    initMatchFilter();
    initLoginValidation();
    initAlertAutoDismiss();
});

function initBackToTop() {
    const backToTopBtn = document.querySelector('.back-to-top');
    
    if (!backToTopBtn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('is-visible');
            backToTopBtn.style.display = 'block'; 
        } else {
            backToTopBtn.classList.remove('is-visible');
            backToTopBtn.style.display = 'none';
        }
    });

    backToTopBtn.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('button[value="supprimer"]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const isConfirmed = confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est définitive.");
            
            if (!isConfirmed) {
                event.preventDefault();
            }
        });
    });
}

function initMatchDateValidation() {
    const dateInput = document.getElementById('date');

    if (!dateInput) return;

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedCurrentDate = `${year}-${month}-${day}`;

    dateInput.setAttribute('min', formattedCurrentDate);
}

function initMatchFilter() {
    const container = document.querySelector('.matchs-container');
    if (!container) return;

    const filterInput = document.createElement('input');
    filterInput.type = 'text';
    filterInput.placeholder = '🔍 Filtrer par lieu (ex: Puteaux, Forest Hill...)';
    filterInput.style.cssText = 'display: block; margin: 10px auto 20px auto; padding: 10px; width: 80%; max-width: 400px; border: 1px solid #ccc; border-radius: 5px;';
    
    container.parentNode.insertBefore(filterInput, container);

    filterInput.addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase();
        const matchCards = container.querySelectorAll('.match-card');

        matchCards.forEach(card => {
            const venueElement = card.querySelector('p:nth-of-type(2)') || card.querySelector('.match-info span');
            if (venueElement) {
                const venueText = venueElement.textContent.toLowerCase();
                if (venueText.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.style.display = 'none';
                    card.style.setProperty('display', 'none', 'important');
                }
            }
        });
    });
}

function initLoginValidation() {
    const loginForm = document.querySelector('form[method="POST"]');
    if (!loginForm) return;

    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;

    const messageContainer = document.createElement('p');
    messageContainer.style.textAlign = 'center';
    passwordInput.parentNode.insertBefore(messageContainer, passwordInput.nextSibling);

    passwordInput.addEventListener('input', (event) => {
        const passwordLength = event.target.value.length;
        if (passwordLength > 0 && passwordLength < 4) {
            messageContainer.textContent = '⚠️ Le mot de passe semble très court.';
            messageContainer.style.color = 'orange';
        } else {
            messageContainer.textContent = '';
        }
    });
}

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