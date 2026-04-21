<?php
$pageTitle    = 'Gestion des réservations — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="page-inner">
    <div class="container" style="padding-bottom: 4rem;">

        <div style="padding-top: 2rem; margin-bottom: 2rem;">
            <a href="/admin" class="admin-back-link">← Retour au dashboard</a>
        </div>

        <h1 class="page-title">RÉSERVATIONS</h1>
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

        <!-- Filtre date -->

        <form action="/admin/reservations" method="GET" class="admin-date-filter">
            <label for="date" class="admin-label">Filtrer par date</label>
            <div class="admin-inline-fields">
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>"
                       class="admin-input admin-input--small">
                <button type="submit" class="admin-btn admin-btn--edit">Valider</button>
            </div>
        </form>

        <!-- Tableau réservations -->

        <?php if (empty($reservations)): ?>
            <p style="font-family: var(--font-ui); opacity: 0.5; margin-top: 2rem; font-style: italic;">
                Aucune réservation pour le <?= htmlspecialchars(date('d/m/Y', strtotime($date))) ?>.
            </p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Heure</th>
                        <th>Couverts</th>
                        <th>Allergies</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?></td>
                            <td><?= htmlspecialchars($reservation['email']) ?></td>
                            <td><?= htmlspecialchars(substr($reservation['heure'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($reservation['nombre_convives']) ?></td>
                            <td><?= htmlspecialchars($reservation['allergies'] ?? '—') ?></td>
                            <td class="admin-actions">
                                <button class="admin-btn admin-btn--edit"
                                        onclick="toggleForm('edit-resa-<?= $reservation['reservation_id'] ?>')">
                                    Modifier
                                </button>
                                <form action="/admin/reservation/<?= $reservation['reservation_id'] ?>/supprimer"
                                      method="POST" onsubmit="return confirm('Supprimer cette réservation ?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="admin-btn admin-btn--delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Formulaire modification -->
                         
                        <tr id="edit-resa-<?= $reservation['reservation_id'] ?>" style="display: none;">
                            <td colspan="6">
                                <form action="/admin/reservation/<?= $reservation['reservation_id'] ?>/modifier"
                                      method="POST" class="admin-inline-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <div class="admin-inline-fields">
                                        <input type="date" name="date" value="<?= htmlspecialchars($reservation['date']) ?>"
                                               required class="admin-input admin-input--small">
                                        <input type="time" name="heure" value="<?= htmlspecialchars(substr($reservation['heure'], 0, 5)) ?>"
                                               required class="admin-input admin-input--small">
                                        <input type="number" name="nombre_convives" value="<?= htmlspecialchars($reservation['nombre_convives']) ?>"
                                               min="1" required class="admin-input admin-input--small" placeholder="Couverts">
                                        <input type="text" name="allergies" value="<?= htmlspecialchars($reservation['allergies'] ?? '') ?>"
                                               class="admin-input" placeholder="Allergies (optionnel)">
                                    </div>
                                    <div class="admin-inline-actions">
                                        <button type="submit" class="admin-btn admin-btn--save">Enregistrer</button>
                                        <button type="button" class="admin-btn admin-btn--cancel"
                                                onclick="toggleForm('edit-resa-<?= $reservation['reservation_id'] ?>')">Annuler</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<script>
function toggleForm(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
