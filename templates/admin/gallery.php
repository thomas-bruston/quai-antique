<?php
$pageTitle    = 'Gestion de la galerie — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="page-inner">
    <div class="container" style="padding-bottom: 4rem;">

        <div style="padding-top: 2rem; margin-bottom: 2rem;">
            <a href="/admin" class="admin-back-link">← Retour au dashboard</a>
        </div>

        <h1 class="page-title">GALERIE</h1>
        <div class="gold-separator"></div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Grille photos -->

        <div class="admin-gallery-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="admin-gallery-item">
                    <img
                        src="<?= htmlspecialchars($photo->getPhotoPath()) ?>"
                        alt="<?= htmlspecialchars($photo->getTitre()) ?>">
                    
                    <p class="admin-gallery-titre"><?= htmlspecialchars($photo->getTitre()) ?></p>

                    <div class="admin-actions" style="margin-top: 0.5rem;">
                        <button class="admin-btn admin-btn--edit"
                                onclick="toggleForm('edit-photo-<?= $photo->getGalleryId() ?>')">
                            Modifier
                        </button>
                        <form action="/admin/galerie/<?= $photo->getGalleryId() ?>/supprimer" method="POST"
                              onsubmit="return confirm('Supprimer cette photo ?')">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <button type="submit" class="admin-btn admin-btn--delete">Supprimer</button>
                        </form>
                    </div>

                    <!-- Formulaire modif -->

                    <div id="edit-photo-<?= $photo->getGalleryId() ?>" style="display: none; margin-top: 0.75rem;">
                        <form action="/admin/galerie/<?= $photo->getGalleryId() ?>/modifier"
                              method="POST" enctype="multipart/form-data" class="admin-inline-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="text" name="titre" value="<?= htmlspecialchars($photo->getTitre()) ?>"
                                   placeholder="Titre" required class="admin-input admin-input--full"
                                   style="margin-bottom: 0.5rem;">
                            <label class="admin-label">Nouvelle photo (optionnel)</label>
                            <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="admin-file-input">
                            <div class="admin-inline-actions">
                                <button type="submit" class="admin-btn admin-btn--save">Enregistrer</button>
                                <button type="button" class="admin-btn admin-btn--cancel"
                                        onclick="toggleForm('edit-photo-<?= $photo->getGalleryId() ?>')">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

         <!-- Formulaire ajout photo -->

        <div class="admin-add-section">
            <button class="admin-btn admin-btn--add" onclick="toggleForm('add-photo')">+ Ajouter une photo</button>
            <div id="add-photo" style="display: none; margin-top: 1rem;">
                <form action="/admin/galerie/ajouter" method="POST" enctype="multipart/form-data" class="admin-inline-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="text" name="titre" placeholder="Titre de la photo" required
                           class="admin-input admin-input--full" style="margin-bottom: 0.75rem;">
                    <label class="admin-label">Photo (JPG, PNG, WebP — max 5 Mo)</label>
                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" required class="admin-file-input">
                    <div class="admin-inline-actions" style="margin-top: 0.75rem;">
                        <button type="submit" class="admin-btn admin-btn--save">Ajouter</button>
                        <button type="button" class="admin-btn admin-btn--cancel"
                                onclick="toggleForm('add-photo')">Annuler</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function toggleForm(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
