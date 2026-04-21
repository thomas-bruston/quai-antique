<?php
$pageTitle       = 'La Carte — Le Quai Antique';
$metaDescription = 'Découvrez la carte et les menus du restaurant Le Quai Antique, Chef Arnaud Michant à Chambéry.';
$bodyClass       = 'page-card';

ob_start();
?>

<!-- Menus -->

<section class="card-section" id="menus" aria-label="Nos menus">

   <div class="card-grid">
    <?php foreach ($menus as $menu): ?>
        <article class="card-column" aria-label="<?= htmlspecialchars($menu->getTitre()) ?>">
            <h2 class="card-column-title"><?= htmlspecialchars(strtoupper($menu->getTitre())) ?></h2>
            <p class="card-column-sequences"><strong><?= $menu->getNombreSequences() ?> séquences</strong></p>
            <p class="card-column-price"><?= $menu->getPrix() ?>€</p>
            <div class="card-dish-separator"></div>
            <?php foreach ($menu->getDishes() as $dish): ?>
                <div class="card-dish">
                    <p class="card-dish-name"><?= htmlspecialchars(strtoupper($dish['titre'])) ?></p>
                    <p class="card-dish-desc"><?= htmlspecialchars($dish['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </article>
    <?php endforeach; ?>
</div>

</section>

<!-- Carte -->
 
<section class="card-section" id="carte" aria-label="La carte">

        <div class="card-grid">
    <?php foreach ($dishes as $categoryTitre => $categoryDishes): ?>
        <article class="card-column" aria-label="<?= htmlspecialchars($categoryTitre) ?>">
            <h3 class="card-column-title"><?= htmlspecialchars(strtoupper($categoryTitre)) ?></h3>
            <?php foreach ($categoryDishes as $dish): ?>
                <div class="card-dish">
                    <p class="card-dish-name"><?= htmlspecialchars(strtoupper($dish->getTitre())) ?></p>
                    <p class="card-dish-desc"><?= htmlspecialchars($dish->getDescription()) ?></p>
                    <p class="card-column-price"><?= htmlspecialchars($dish->getPrix()) ?>€</p>
                    <div class="card-dish-separator" aria-hidden="true"></div>
                </div>
            <?php endforeach; ?>
        </article>
    <?php endforeach; ?>
</div>

</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
