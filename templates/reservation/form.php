<?php
$pageTitle    = 'Réservation — Le Quai Antique';
$headerSimple = true;
$scripts      = ['/js/convives.js', '/js/reservation.js'];

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card form-card--large">

        <h1 class="form-card-title">RÉSERVATION</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/reservation" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="heure" id="heure-hidden" value="">
            <input type="hidden" name="nombre_convives" id="nombre_convives" value="1">

            <!-- Couverts -->

            <div class="form-group">
                <label class="form-label">Couverts</label>
                <div class="convives-selector" role="group" aria-label="Nombre de couverts">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <button
                            type="button"
                            class="convives-btn <?= $i === 1 ? 'active' : '' ?>"
                            data-value="<?= $i ?>"
                            aria-label="<?= $i ?> couvert<?= $i > 1 ? 's' : '' ?>"
                            aria-pressed="<?= $i === 1 ? 'true' : 'false' ?>"
                        ><?= $i ?></button>
                    <?php endfor; ?>
                </div>
            </div>

            <hr class="form-card-separator">

            <!-- Date -->

            <div class="form-group">
                <label for="date" class="form-label">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    Date
                </label>
                <input
                    type="date"
                    id="date"
                    name="date"
                    class="form-control"
                    min="<?= date('Y-m-d') ?>"
                    required
                    aria-required="true"
                >
            </div>

            <hr class="form-card-separator">

            <!-- Horaires -->

            <div class="form-group">
                <label class="form-label">
                    <i class="fa-regular fa-clock" aria-hidden="true"></i>
                    Horaires
                </label>

                <!-- Message creneaux -->

                <p class="slots-placeholder" id="slots-placeholder">
                    Sélectionnez une date pour voir les créneaux disponibles.
                </p>

                <!-- Grille des créneaux -->

                <div
                    class="slots-grid"
                    id="slots-grid"
                    role="group"
                    aria-label="Créneaux horaires disponibles"
                    style="display: none;">
                </div>

            <hr class="form-card-separator">

            <!-- Allergies -->
             
            <div class="form-group">
                <label for="allergies" class="form-label">Allergies</label>
                <input
                    type="text"
                    id="allergies"
                    name="allergies"
                    class="form-control"
                    value="<?= htmlspecialchars($userAllergies ?? '') ?>"
                    placeholder="ex : soja, lactose"
                >
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-card btn-card-full" id="btn-reserver" disabled>
                    RÉSERVER
                </button>
            </div>

        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
