<?php
$pageTitle    = 'Administration — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="admin-dashboard">

    <div class="admin-dashboard-header">
        <h1 class="admin-dashboard-title">ADMINISTRATION</h1>
        <p class="admin-dashboard-sub">ARNAUD MICHANT</p>
    </div>

    <nav aria-label="Menu administration">
        <ul class="admin-dashboard-list">
            <li><a href="/" class="admin-dashboard-link">Accueil</a></li>
            <li><a href="/admin/carte" class="admin-dashboard-link">La Carte</a></li>
            <li><a href="/admin/galerie" class="admin-dashboard-link">Galerie</a></li>
            <li><a href="/admin/reservations" class="admin-dashboard-link">Réservations</a></li>
            <li><a href="/admin/messages" class="admin-dashboard-link">Messages</a></li>
            <li><a href="/admin/parametres" class="admin-dashboard-link">Paramètres</a></li>
            <li class="admin-dashboard-divider"></li>
            <li><a href="/deconnexion" class="admin-dashboard-link admin-dashboard-link--logout">Se déconnecter</a></li>
        </ul>
    </nav>

</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
