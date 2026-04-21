<?php
$pageTitle    = 'Nous contacter — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="form-page-wrapper">
    <div class="form-card">

        <h1 class="form-card-title">NOUS CONTACTER</h1>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                Votre message a bien été envoyé. Nous vous répondrons dans les plus brefs délais.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/contact" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="form-group">
                <label for="nom" class="form-label">Nom</label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    class="form-control"
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
                    required
                    aria-required="true"
                >
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
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
                <label for="message" class="form-label">Message</label>
                <textarea
                    id="message"
                    name="message"
                    class="form-control"
                    rows="5"
                    required
                    aria-required="true"
                                        ></textarea>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn-card btn-card-full">ENVOYER</button>
            </div>

        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
