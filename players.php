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

    <main class="matchs-page">

        <h2 class="matchs-greeting">
            Joueurs du match du <?= $slot->getFormattedDate() ?>
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
                        <li style="padding:12px 16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--bg-white);">
                            <strong><?= htmlspecialchars($p->getFullName()) ?></strong><br>
                            Niveau : <?= $p->getLevel() ?><br>
                            <span style="color:var(--text-soft); font-size:0.85rem;">
                                <?= htmlspecialchars($p->getEmail()) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </div>

    </main>


    <?php include "footer.php"; ?>

</body>

</html>