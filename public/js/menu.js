
// Menu burger & navigation overlay

document.addEventListener('DOMContentLoaded', () => {
    const burger  = document.querySelector('.menu-burger');
    const overlay = document.getElementById('nav-overlay');
    const close   = document.querySelector('.nav-overlay-close');

    if (!burger || !overlay) return;

    const openMenu = () => {
        overlay.hidden = false;
        burger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';

        // Focus bouton fermre

        requestAnimationFrame(() => close?.focus());
    };

    const closeMenu = () => {
        overlay.hidden = true;
        burger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        burger.focus();
    };

    burger.addEventListener('click', openMenu);
    close?.addEventListener('click', closeMenu);

    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.hidden) {
            closeMenu();
        }
    });

    // Fermer en cliquant en dehors du contenu
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeMenu();
    });
});
