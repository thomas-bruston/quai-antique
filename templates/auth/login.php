<?php
$pageTitle    = 'Connexion — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card">

        <h1 class="form-card-title">CONNEXION</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/connexion" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

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
                        autocomplete="current-password"
                        required
                        aria-required="true"
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
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-card btn-card-full">CONNEXION</button>
            </div>
        </form>

        <a href="/inscription" class="form-card-link">Pas encore inscrit ?</a>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
