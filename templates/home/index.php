<?php
$pageTitle       = 'Le Quai Antique — Arnaud Michant';
$metaDescription = 'Le Quai Antique, restaurant gastronomique du Chef Arnaud Michant à Chambéry. Une cuisine sans artifice, un voyage dans les saveurs de la Savoie.';
$bodyClass       = 'page-home';

ob_start();
?>

<section class="hero" aria-label="Présentation du restaurant">
    <p class="hero-tagline">Découvrez notre restaurant, un havre gastronomique<br> au sein de nos plus belles montagnes.</p>
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
