document.addEventListener('DOMContentLoaded', () => {

    initDeleteConfirmation();

    initBackToTopButton();

    initMinDateValidation();
});


function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('button[value="supprimer"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const confirmed = confirm("Êtes-vous sûr de vouloir supprimer ce match ? Cette action est irréversible.");
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
}


function initBackToTopButton() {
    const backToTopButton = document.querySelector('.back-to-top');
    
    if (!backToTopButton) return;

    // Show button when scrolling down, hide it when at the top
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    backToTopButton.addEventListener('click', (event) => {
        event.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

function initMinDateValidation() {
    const dateInput = document.getElementById('date');
    
    if (!dateInput) return;

    const today = new Date();
    const year = today.getFullYear();
    // Months are 0-indexed, pad with leading zero if needed
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedToday = `${year}-${month}-${day}`;
    
    // Set the minimum selectable date attribute
    dateInput.setAttribute('min', formattedToday);
}