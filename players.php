<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/autoload.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$controller = new TimeSlotController();
$slot = $controller->read((int)$_GET['id']);

if (!$slot) {
    header("Location: index.php");
    exit;
}

$flashMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['user']['is_admin']) && isset($_POST['remove_user_id'])) {
    $removeUserId = (int)$_POST['remove_user_id'];
    if ($removeUserId > 0) {
        $controller->leave($removeUserId, $slot->getId());
        $flashMessage = 'Joueur supprimé du match.';
    }
}

$players = $controller->getPlayers($slot->getId());
?>

<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joueurs du match</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include "navbar.php"; ?>

    <?php if ($flashMessage): ?>
        <div class="form-message form-message--success" id="toast-message">
            <?= htmlspecialchars($flashMessage) ?>
        </div>
    <?php endif; ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">
            Joueurs du match du <?= $slot->getFormattedDate() ?> à <?= $slot->getFormattedTime() ?>
        </h2>

        <div class="card card-auth">

            <button onclick="history.back()" class="btn-secondary" style="margin-bottom: 20px;">
                ← Retour
            </button>

            <h3 class="card-title">Liste des joueurs</h3>

            <?php if (empty($players)): ?>
                <p class="micro-text">Aucun joueur inscrit pour le moment.</p>
            <?php else: ?>
                <ul style="list-style:none; padding:0; display:flex; flex-direction:column; gap:14px;">
                    <?php foreach ($players as $p): ?>
                        <li style="padding:12px 16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--bg-white); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                            <div>
                                <strong><?= htmlspecialchars($p->getFullName()) ?></strong><br>
                                Niveau : <?= htmlspecialchars($p->getLevel()) ?>
                            </div>
                            <?php if (!empty($_SESSION['user']['is_admin'])): ?>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="remove_user_id" value="<?= $p->getId() ?>">
                                    <button type="submit" class="btn-secondary" style="padding: 8px 12px; font-size:0.9rem;">
                                        Supprimer
                                    </button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </div>

    </main>


    <?php include "footer.php"; ?>
</body>

</html>