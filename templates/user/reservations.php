<?php
$pageTitle    = 'Mes réservations — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card form-card--large">

        <h1 class="form-card-title">MES RÉSERVATIONS</h1>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($reservations)): ?>
            <p style="text-align: center; font-family: var(--font-ui); opacity: 0.6; margin: 2rem 0;">
                Vous n'avez aucune réservation pour le moment.
            </p>
            <a href="/reservation" class="btn-card btn-card-full">FAIRE UNE RÉSERVATION</a>

        <?php else: ?>
            <div class="reservations-list">
                <?php foreach ($reservations as $reservation): ?>
                    <article class="reservation-item">
                        <div class="reservation-info">
                            <p class="reservation-date">
                                <?= htmlspecialchars($reservation->getDateFormatted()) ?>
                                — <?= htmlspecialchars($reservation->getHeureFormatted()) ?>
                            </p>
                            <p class="reservation-details">
                                <?= htmlspecialchars($reservation->getNombreConvives()) ?>
                                couvert<?= $reservation->getNombreConvives() > 1 ? 's' : '' ?>
                                <?php if ($reservation->getAllergies()): ?>
                                    · <?= htmlspecialchars($reservation->getAllergies()) ?>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="reservation-actions">
                            <a href="/mon-compte/reservation/<?= $reservation->getReservationId() ?>/modifier"
                               class="btn-card">
                                Modifier
                            </a>

                            <form action="/mon-compte/reservation/<?= $reservation->getReservationId() ?>/supprimer"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer cette réservation ?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="btn-card btn-card-danger">Supprimer</button>
                            </form>
                        </div>
                    </article>

                    <hr class="form-card-separator">
                <?php endforeach; ?>
            </div>

            <a href="/reservation" class="btn-card btn-card-full" style="margin-top: 1rem;">
                NOUVELLE RÉSERVATION
            </a>
        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
