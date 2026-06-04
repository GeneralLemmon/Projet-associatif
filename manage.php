<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$controller = new TimeSlotController();

// Actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && $id) {
        $controller->delete($id);
    }
    if ($action === 'modifier' && $id) {
        header("Location: edit.php?id=$id");
        exit;
    }
    header("Location: manage.php");
    exit;
}

$slots = $controller->readAll();
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les matchs – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="matchs-page">
        <?php if (empty($slots)): ?>

            <div class="manage-header" style="justify-content: center;">
                <h2 class="matchs-greeting" style="margin-bottom:0">Gérer les matchs</h2>
            </div>

            <div class="empty-state-container">
                <p class="empty-state-text">
                    Aucun match créé pour l'instant.
                </p>
                <a href="create.php" class="btn-primary btn-centered">+ Créer un match</a>
            </div>

        <?php else: ?>

            <div class="manage-header">
                <h2 class="matchs-greeting" style="margin-bottom:0">Gérer les matchs</h2>
                <a href="create.php" class="btn-primary">+ Créer un match</a>
            </div>

            <div class="matchs-container">
                <?php foreach ($slots as $slot): ?>
                    <div class="match-card">
                        <p class="match-date">
                            <?= $slot->getFormattedDate() ?> – <?= $slot->getFormattedTime() ?>
                        </p>
                        <div class="match-info">
                            <img src="Images/level.png" alt="Durée">
                            <span>Durée : <?= $slot->getFormattedDuration() ?></span>
                        </div>
                        <div class="match-info">
                            <img src="Images/lieu.png" alt="Lieu">
                            <span><?= htmlspecialchars($slot->getLocation()) ?></span>
                        </div>

                        <a href="players.php?id=<?= $slot->getId() ?>" class="match-info" style="text-decoration:none;">
                            <img src="Images/player.png" alt="Joueurs">
                            <span><?= $slot->getPlayerCount() ?>/4 Joueurs</span>
                        </a>


                        <div class="match-info">
                            <img src="Images/level.png" alt="Niveau">
                            <span>Niveau : <?= $slot->getLevel() ?></span>
                        </div>

                        <form method="POST" style="display:flex; gap:8px; margin-top:8px">
                            <input type="hidden" name="id" value="<?= $slot->getId() ?>">
                            <button name="action" value="modifier" class="btn-primary" style="flex:1; padding:10px">
                                Modifier
                            </button>
                            <button name="action" value="supprimer" class="btn-secondary" style="flex:1; padding:10px"
                                onclick="return confirm('Supprimer ce match ?')">
                                Supprimer
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </main>

    <?php require "footer.php"; ?>
</body>

</html>