<?php
$pageTitle       = 'Galerie — Le Quai Antique';
$metaDescription = 'Galerie photos du restaurant Le Quai Antique, Chef Arnaud Michant à Chambéry.';
$headerSimple    = true;

ob_start();
?>

<div class="page-inner">
    <div class="gallery-header">
        <h1 class="card-title">LE QUAI ANTIQUE</h1>
        <p class="card-subtitle">GALERIE</p>
    </div>

    <?php if (!empty($photos)): ?>
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
                <figure class="gallery-item">
                    <img
                        src="<?= htmlspecialchars($photo->getPhotoPath()) ?>"
                        alt="<?= htmlspecialchars($photo->getTitre()) ?>"
                        loading="lazy"
                    >
                    <figcaption class="gallery-caption">
                        <?= htmlspecialchars($photo->getTitre()) ?>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="card-empty">Aucune photo disponible pour le moment.</p>
    <?php endif; ?>

    <!-- lien reservation-->

    <div class="gallery-cta">
        <a href="/reservation" class="gallery-cta-btn">RÉSERVER UNE TABLE</a>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
