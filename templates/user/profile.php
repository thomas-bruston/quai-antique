<?php
$pageTitle    = 'Mes informations — Le Quai Antique';
$headerSimple = true;
$scripts      = ['/js/password.js', '/js/convives.js'];

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card">

        <h1 class="form-card-title">MES INFORMATIONS</h1>

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

        <form action="/mon-compte/profil" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="form-group">
                <label for="nom" class="form-label">Nom</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    value="<?= htmlspecialchars($user->getNom()) ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="prenom" class="form-label">Prénom</label>
                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    class="form-control"
                    value="<?= htmlspecialchars($user->getPrenom()) ?>"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars($user->getEmail()) ?>"
                    autocomplete="email"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Nouveau mot de passe</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        autocomplete="new-password"
                        placeholder="Laisser vide pour ne pas changer"
                        aria-describedby="password-hint"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password', 'icon-password')"
                        aria-label="Afficher ou masquer le mot de passe"
                    >
                        <i id="icon-password" class="fa fa-eye"></i>
                    </button>
                </div>
                <span id="password-hint" class="form-error" style="color: rgba(10,10,10,0.5);">
                    10 caractères min, 1 majuscule, 1 chiffre, 1 caractère spécial
                </span>
            </div>

            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="form-control"
                        autocomplete="new-password"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password_confirm', 'icon-password-confirm')"
                        aria-label="Afficher ou masquer la confirmation"
                    >
                        <i id="icon-password-confirm" class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="allergies" class="form-label">Allergies</label>
                <input
                    type="text"
                    id="allergies"
                    name="allergies"
                    class="form-control"
                    value="<?= htmlspecialchars($user->getAllergies() ?? '') ?>"
                    placeholder="ex : gluten, lactose"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Convives par défaut</label>
                <div class="convives-selector" role="group" aria-label="Nombre de convives par défaut">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <button
                            type="button"
                            class="convives-btn <?= $user->getNombreConvivesDefaut() == $i ? 'active' : '' ?>"
                            data-value="<?= $i ?>"
                            aria-label="<?= $i ?> convive<?= $i > 1 ? 's' : '' ?>"
                            aria-pressed="<?= $user->getNombreConvivesDefaut() == $i ? 'true' : 'false' ?>"
                        ><?= $i ?></button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="nombre_convives_defaut" value="<?= $user->getNombreConvivesDefaut() ?>">
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <button type="submit" class="btn-card btn-card-full">VALIDER LES MODIFICATIONS</button>
            </div>

        </form>

        <hr class="form-card-separator">

        <!-- Suppression du compte -->
         
        <form action="/mon-compte/supprimer" method="POST" novalidate
              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <button type="submit" class="form-card-link" style="cursor: pointer; background: none; border: none; color: #e43606ff; font-size:1rem;">
                Supprimer mon compte
            </button>
        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
