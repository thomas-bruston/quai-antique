<?php
$pageTitle    = 'Messages — Le Quai Antique';
$headerSimple = true;

ob_start();
?>

<div class="page-inner">
    <div class="container" style="padding-bottom: 4rem;">

        <div style="padding-top: 2rem; margin-bottom: 2rem;">
            <a href="/admin" class="admin-back-link">← Retour au dashboard</a>
        </div>

        <h1 class="page-title">MESSAGES</h1>
        <div class="gold-separator"></div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($messages)): ?>
            <p style="font-family: var(--font-ui); opacity: 0.5; margin-top: 2rem; font-style: italic;">
                Aucun message reçu pour le moment.
            </p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= htmlspecialchars($message['nom']) ?></td>
                            <td><?= htmlspecialchars($message['prenom']) ?></td>
                            <td><?= htmlspecialchars($message['email']) ?></td>
                            <td>
                                <span class="admin-message-preview">
                                    <?= htmlspecialchars(mb_substr($message['message'], 0, 80)) ?>
                                    <?= mb_strlen($message['message']) > 80 ? '...' : '' ?>
                                </span>
                                <?php if (mb_strlen($message['message']) > 80): ?>
                                    <button class="admin-btn admin-btn--edit"
                                            style="margin-top: 0.25rem; font-size: 0.65rem;"
                                            onclick="toggleForm('msg-<?= htmlspecialchars($message['id']) ?>')">
                                        Lire
                                    </button>
                                    <div id="msg-<?= htmlspecialchars($message['id']) ?>" style="display: none; margin-top: 0.5rem;">
                                        <p style="font-family: var(--font-body); font-size: 0.9rem; opacity: 0.8; line-height: 1.6;">
                                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?= htmlspecialchars(date('d/m/Y H:i', strtotime($message['date_envoi']))) ?>
                            </td>
                            <td>
                                <form action="/admin/messages/<?= htmlspecialchars($message['id']) ?>/supprimer"
                                      method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="admin-btn admin-btn--delete">Supprimer</button>
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
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/base.php';
?>
