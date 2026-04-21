<?php
$pageTitle       = 'Mentions légales — Le Quai Antique';
$metaDescription = 'Mentions légales du restaurant Le Quai Antique, Chambéry.';
$headerSimple    = true;

ob_start();
?>

<div class="page-inner">
    <div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">
        <h1 class="page-title">MENTIONS LÉGALES</h1>
        <div class="gold-separator"></div>

        <p style="text-align: center; font-family: var(--font-ui); opacity: 0.6; margin-top: 2rem;">
            Mettre les mentions légales ici.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
