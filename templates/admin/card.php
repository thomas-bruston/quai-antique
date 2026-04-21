<?php
$pageTitle    = 'Gestion de la carte — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="page-inner">
    <div class="container" style="padding-bottom: 4rem;">

        <!-- Retour dashboard -->
        <div style="padding-top: 2rem; margin-bottom: 2rem;">
            <a href="/admin" class="admin-back-link">← Retour au dashboard</a>
        </div>

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

        <!-- Carte -->

        <section class="admin-section">
            <h1 class="page-title">LA CARTE</h1>
            <div class="gold-separator"></div>

            <!-- plats -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dishes as $dish): ?>
                        <tr>
                            <td><?= htmlspecialchars($dish->getTitre()) ?></td>
                            <td>
                                <?php foreach ($categories as $cat): ?>
                                    <?php if ($cat->getCategoryId() === $dish->getCategoryId()): ?>
                                        <?= htmlspecialchars($cat->getTitre()) ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </td>
                            <td><?= $dish->getPrix() ?>€</td>
                            <td class="admin-actions">
                                <button class="admin-btn admin-btn--edit"
                                        onclick="toggleForm('edit-dish-<?= $dish->getDishId() ?>')">
                                    Modifier
                                </button>
                                <form action="/admin/plat/<?= $dish->getDishId() ?>/supprimer" method="POST"
                                      onsubmit="return confirm('Supprimer ce plat ?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="admin-btn admin-btn--delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Formulaire modif plat -->

                        <tr id="edit-dish-<?= $dish->getDishId() ?>" style="display: none;">
                            <td colspan="4">
                                <form action="/admin/plat/<?= $dish->getDishId() ?>/modifier" method="POST" class="admin-inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <div class="admin-inline-fields">
                                        <input type="text" name="titre" value="<?= htmlspecialchars($dish->getTitre()) ?>"
                                               placeholder="Titre" required class="admin-input">
                                        <select name="category_id" class="admin-input" required>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat->getCategoryId() ?>"
                                                    <?= $cat->getCategoryId() === $dish->getCategoryId() ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat->getTitre()) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="number" name="prix" value="<?= $dish->getPrix() ?>"
                                               placeholder="Prix €" required min="1" class="admin-input admin-input--small">
                                    </div>
                                    <textarea name="description" rows="2" required class="admin-input admin-input--full"
                                              placeholder="Description"><?= htmlspecialchars($dish->getDescription()) ?></textarea>
                                    <div class="admin-inline-actions">
                                        <button type="submit" class="admin-btn admin-btn--save">Enregistrer</button>
                                        <button type="button" class="admin-btn admin-btn--cancel"
                                                onclick="toggleForm('edit-dish-<?= $dish->getDishId() ?>')">Annuler</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Formulaire ajout plat -->

            <div class="admin-add-section">
                <button class="admin-btn admin-btn--add" onclick="toggleForm('add-dish')">+ Ajouter un plat</button>
                <div id="add-dish" style="display: none;">
                    <form action="/admin/plat/ajouter" method="POST" class="admin-inline-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="admin-inline-fields">
                            <input type="text" name="titre" placeholder="Titre" required class="admin-input">
                            <select name="category_id" class="admin-input" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->getCategoryId() ?>">
                                        <?= htmlspecialchars($cat->getTitre()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" name="prix" placeholder="Prix €" required min="1"
                                   class="admin-input admin-input--small">
                        </div>
                        <textarea name="description" rows="2" required class="admin-input admin-input--full"
                                  placeholder="Description"></textarea>
                        <div class="admin-inline-actions">
                            <button type="submit" class="admin-btn admin-btn--save">Ajouter</button>
                            <button type="button" class="admin-btn admin-btn--cancel"
                                    onclick="toggleForm('add-dish')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Menus -->

        <section class="admin-section" style="margin-top: 4rem;">
            <h2 class="page-title">LES MENUS</h2>
            <div class="gold-separator"></div>

            <!-- Liste menus -->

            <?php foreach ($menus as $menu): ?>
                <div class="admin-menu-item">
                    <div class="admin-menu-header">
                        <div>
                            <span class="admin-menu-title"><?= htmlspecialchars($menu->getTitre()) ?></span>
                            <span class="admin-menu-meta">
                                <?= $menu->getNombreSequences() ?> séquences — <?= $menu->getPrix() ?>€
                            </span>
                        </div>
                        <div class="admin-actions">
                            <button class="admin-btn admin-btn--edit"
                                    onclick="toggleForm('edit-menu-<?= $menu->getMenuId() ?>')">
                                Modifier
                            </button>
                            <form action="/admin/menu/<?= $menu->getMenuId() ?>/supprimer" method="POST"
                                  onsubmit="return confirm('Supprimer ce menu ?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="admin-btn admin-btn--delete">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    <!-- Plats menu -->

                    <ul class="admin-menu-dishes">
                        <?php foreach ($menu->getDishes() as $dish): ?>
                            <li>— <?= htmlspecialchars($dish['titre']) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Formulaire modif menu -->

                    <div id="edit-menu-<?= $menu->getMenuId() ?>" style="display: none;">
                        <form action="/admin/menu/<?= $menu->getMenuId() ?>/modifier" method="POST" class="admin-inline-form">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <div class="admin-inline-fields">
                                <input type="text" name="titre" value="<?= htmlspecialchars($menu->getTitre()) ?>"
                                       placeholder="Titre" required class="admin-input">
                                <input type="number" name="nombre_sequences" value="<?= $menu->getNombreSequences() ?>"
                                       placeholder="Séquences" required min="1" class="admin-input admin-input--small">
                                <input type="number" name="prix" value="<?= $menu->getPrix() ?>"
                                       placeholder="Prix €" required min="1" class="admin-input admin-input--small">
                            </div>

                            <!-- Sélection plats -->

                            <p class="admin-label">Plats du menu :</p>
                            <div class="admin-dishes-checkboxes">
                                <?php
                                $menuDishIds = array_column($menu->getDishes(), 'dish_id');
                                foreach ($dishes as $dish):
                                ?>
                                    <label class="admin-checkbox-label">
                                        <input type="checkbox" name="dish_ids[]"
                                               value="<?= $dish->getDishId() ?>"
                                               <?= in_array($dish->getDishId(), $menuDishIds) ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($dish->getTitre()) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="admin-inline-actions">
                                <button type="submit" class="admin-btn admin-btn--save">Enregistrer</button>
                                <button type="button" class="admin-btn admin-btn--cancel"
                                        onclick="toggleForm('edit-menu-<?= $menu->getMenuId() ?>')">Annuler</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Formulaire ajout menu -->
             
            <div class="admin-add-section">
                <button class="admin-btn admin-btn--add" onclick="toggleForm('add-menu')">+ Ajouter un menu</button>
                <div id="add-menu" style="display: none;">
                    <form action="/admin/menu/ajouter" method="POST" class="admin-inline-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="admin-inline-fields">
                            <input type="text" name="titre" placeholder="Titre" required class="admin-input">
                            <input type="number" name="nombre_sequences" placeholder="Séquences"
                                   required min="1" class="admin-input admin-input--small">
                            <input type="number" name="prix" placeholder="Prix €"
                                   required min="1" class="admin-input admin-input--small">
                        </div>
                        <p class="admin-label">Plats du menu :</p>
                        <div class="admin-dishes-checkboxes">
                            <?php foreach ($dishes as $dish): ?>
                                <label class="admin-checkbox-label">
                                    <input type="checkbox" name="dish_ids[]" value="<?= $dish->getDishId() ?>">
                                    <?= htmlspecialchars($dish->getTitre()) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-inline-actions">
                            <button type="submit" class="admin-btn admin-btn--save">Ajouter</button>
                            <button type="button" class="admin-btn admin-btn--cancel"
                                    onclick="toggleForm('add-menu')">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

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
