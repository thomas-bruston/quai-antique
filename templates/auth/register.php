<?php
$pageTitle    = 'Inscription — Le Quai Antique';
$headerSimple = true;
$scripts      = ['/js/password.js', '/js/convives.js'];

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card">

        <h1 class="form-card-title">INSCRIPTION</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/inscription" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="form-group">
                <label for="nom" class="form-label">Nom</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
                    autocomplete="family-name"
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
                    autocomplete="given-name"
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
                    autocomplete="email"
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        autocomplete="new-password"
                        required
                        aria-required="true"
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
                <span id="password-hint" class="form-error" style="color: rgba(10,10,10,0.5); margin-top: 0.35rem;">
                    10 caractères min, 1 majuscule, 1 chiffre, 1 caractère spécial
                </span>
            </div>

            <div class="form-group">
                <label for="password_confirm" class="form-label">Confirmez votre mot de passe</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        class="form-control"
                        autocomplete="new-password"
                        required
                        aria-required="true"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword('password_confirm', 'icon-password-confirm')"
                        aria-label="Afficher ou masquer la confirmation du mot de passe"
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
                    placeholder="ex : gluten, lactose"
                    aria-describedby="allergies-hint"
                >
                <span id="allergies-hint" class="form-error" style="color: rgba(10,10,10,0.4);">
                    Optionnel — sera pré-rempli lors de vos réservations
                </span>
            </div>

            <!-- Sélect convives -->

            <div class="form-group">
                <label class="form-label">Convives</label>
                <div class="convives-selector" role="group" aria-label="Nombre de convives par défaut">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <button
                            type="button"
                            class="convives-btn <?= $i === 1 ? 'active' : '' ?>"
                            data-value="<?= $i ?>"
                            aria-label="<?= $i ?> convive<?= $i > 1 ? 's' : '' ?>"
                            aria-pressed="<?= $i === 1 ? 'true' : 'false' ?>"
                        ><?= $i ?></button>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="nombre_convives_defaut" name="nombre_convives_defaut" value="1">
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-card btn-card-full">S'INSCRIRE</button>
            </div>
        </form>

        <a href="/connexion" class="form-card-link">Déjà inscrit ? Se connecter</a>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
