// Sélect convives inscription et réservation


document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.convives-selector').forEach(selector => {
        const hidden = selector.closest('form')?.querySelector('input[name="nombre_convives_defaut"], input[name="nombre_convives"]');
        const buttons = selector.querySelectorAll('.convives-btn');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Désactiver tous les boutons
                buttons.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-pressed', 'false');
                });

                // Activer le bouton cliqué
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');

                // Mettre à jour le champ caché
                if (hidden) hidden.value = btn.dataset.value;
            });
        });
    });
});
