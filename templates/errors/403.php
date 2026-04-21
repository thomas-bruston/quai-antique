<?php $pageTitle = 'Accès refusé'; ?>
<?php ob_start(); ?>

<main id="main-content" style="text-align:center; padding: 80px 20px;">
    <h1 style="font-size: 5rem; color: #000000ff;">403</h1>
    <h2>Accès refusé</h2>
    <p>Vous n'avez pas les droits pour accéder à cette page.</p>
    <a href="/" style="margin-top: 20px; display: inline-block;">Retour à l'accueil</a>
</main>

<?php $content = ob_get_clean(); ?>
<?php require_once __DIR__ . '/../../templates/layout/base.php'; ?>