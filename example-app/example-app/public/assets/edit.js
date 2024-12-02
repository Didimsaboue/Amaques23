document.addEventListener('DOMContentLoaded', (event) => {
    const modifierBtns = document.querySelectorAll('.modifierBtn');
    const supprimerForms = document.querySelectorAll('.supprimerForm');
    const modal = document.getElementById('formModal');
    const closeModalBtn = modal.querySelector('.close');
    const form = modal.querySelector('form');

    // Gestion de l'affichage du formulaire de modification
    modifierBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            // Récupérer les données de l'utilisateur à partir de la ligne du tableau
            const id = btn.getAttribute('data-id');
            const row = btn.closest('tr');
            const nom = row.cells[0].innerText;
            const email = row.cells[1].innerText;
            const motDePasse = row.cells[2].innerText;
            const role = row.cells[3].innerText;

            // Pré-remplir le formulaire de modification
            form.setAttribute('action', `/utilisateurs/${id}/modifier`);
            form.querySelector('#name').value = nom;
            form.querySelector('#email').value = email;
            form.querySelector('#password').value = motDePasse;
            form.querySelector('#role').value = role;

            // Afficher le modal
            modal.style.display = 'block';
        });
    });

    // Gestion de la suppression
    supprimerForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                form.submit();
            }
        });
    });

    // Gestion de la fermeture du modal
    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

