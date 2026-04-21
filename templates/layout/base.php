<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Le Quai Antique — Restaurant gastronomique du Chef Arnaud Michant à Chambéry.') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Le Quai Antique — Arnaud Michant') ?></title>

    <!-- Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Cinzel:wght@400;600&family=Jost:wght@300;400&display=swap" rel="stylesheet">

    <!-- FontAwesome -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/pages.css">
    <link rel="stylesheet" href="/css/gallery.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '') ?>">

    <?php require_once __DIR__ . '/header.php'; ?>

    <main id="main-content">
        <?= $content ?? '' ?>
    </main>

    <?php require_once __DIR__ . '/footer.php'; ?>

    <!-- JS -->
     
    <script src="/js/menu.js"></script>
    <script src="/js/password.js"></script>
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
