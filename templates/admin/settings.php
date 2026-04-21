<?php
$pageTitle    = 'Paramètres — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div style="padding: 6rem 4rem 0;">
    <a href="/admin" class="admin-back-link">← Retour au dashboard</a>
</div>
<div class="form-page-wrapper">
    <div class="form-card">

        <h1 class="form-card-title">PARAMÈTRES</h1>

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

        

        <form action="/admin/parametres" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="form-group">
                <label for="heure_ouverture" class="form-label">Heure d'ouverture</label>
                <input
                    type="time"
                    id="heure_ouverture"
                    name="heure_ouverture"
                    class="form-control"
                    value="<?= htmlspecialchars(substr($settings->getHeureOuverture(), 0, 5)) ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="heure_fermeture" class="form-label">Heure de fermeture</label>
                <input
                    type="time"
                    id="heure_fermeture"
                    name="heure_fermeture"
                    class="form-control"
                    value="<?= htmlspecialchars(substr($settings->getHeureFermeture(), 0, 5)) ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="max_convives" class="form-label">Nombre maximum de convives</label>
                <input
                    type="number"
                    id="max_convives"
                    name="max_convives"
                    class="form-control"
                    value="<?= htmlspecialchars($settings->getMaxConvives()) ?>"
                    min="1"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn-card btn-card-full">ENREGISTRER</button>
            </div>

        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
