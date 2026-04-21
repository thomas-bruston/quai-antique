<header class="site-header <?= isset($headerSimple) && $headerSimple ? 'header-simple' : '' ?>" role="banner">

    <!-- Bouton menu burger  -->

    <div class="header-left">
        <button
            class="menu-burger"
            aria-label="Ouvrir le menu de navigation"
            aria-expanded="false"
            aria-controls="nav-overlay"
        >
            <span class="burger-line"></span>
            <span class="burger-line"></span>
            <span class="burger-line"></span>
        </button>
    </div>

    <!-- Titre  -->

    <div class="header-brand">
        <a href="/" class="header-brand-link" aria-label="Le Quai Antique — Accueil">
            <span class="header-brand-name">LE QUAI ANTIQUE</span>
            <span class="header-brand-sub">ARNAUD MICHANT</span>
        </a>
    </div>

    <!-- Lien accueil -->

    <nav class="header-right" aria-label="Navigation principale">
        <?php if (isset($headerSimple) && $headerSimple): ?>
            <a href="/" class="header-nav-link">ACCUEIL</a>
        <?php else: ?>
            <a href="/reservation" class="header-nav-link">RÉSERVER</a>
        <?php endif; ?>
    </nav>

</header>

<!-- Overlay nav = burger menu -->

<div id="nav-overlay" class="nav-overlay" role="dialog" aria-modal="true" aria-label="Menu de navigation" hidden>

    <button class="nav-overlay-close" aria-label="Fermer le menu">✕</button>

    <nav aria-label="Menu principal">
        <ul class="nav-overlay-list">
            <li><a href="/" class="nav-overlay-link">Accueil</a></li>
            <li><a href="/carte" class="nav-overlay-link">La Carte</a></li>
            <li><a href="/galerie" class="nav-overlay-link">Galerie</a></li>
            <li><a href="/reservation" class="nav-overlay-link">Réserver</a></li>
            <li><a href="/contact" class="nav-overlay-link">Nous contacter</a></li>
            <li class="nav-overlay-divider"></li>

            <?php
            $sessionUser = $_SESSION['user'] ?? null;
            if ($sessionUser): ?>
                <li><a href="/mon-compte/reservations" class="nav-overlay-link">Mes réservations</a></li>
                <li><a href="/mon-compte/profil" class="nav-overlay-link">Mes informations</a></li>
                <li><a href="/deconnexion" class="nav-overlay-link nav-overlay-link--logout">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="/connexion" class="nav-overlay-link">Se connecter</a></li>
                <li><a href="/inscription" class="nav-overlay-link">Créer un compte</a></li>
            <?php endif; ?>
        </ul>
    </nav>

</div>
