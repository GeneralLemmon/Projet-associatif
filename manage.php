<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$controller = new TimeSlotController();
$message = '';

// Actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'supprimer' && $id) {
        $slot = $controller->read($id);
        $players = $controller->getPlayers($id);
        $playerIds = array_map(fn($p) => $p->getId(), $players);
        $adminId = $_SESSION['user']['id'];
        $playerIds = array_filter($playerIds, fn($userId) => $userId !== $adminId);

        if ($slot && !empty($playerIds)) {
            $messageText = sprintf(
                "Le match du %s à %s au lieu '%s' a été supprimé. Vous n'êtes plus inscrit.",
                $slot->getFormattedDate(),
                $slot->getFormattedTime(),
                $slot->getLocation()
            );

            $notificationController = new NotificationController();
            $notificationId = $notificationController->create($messageText, null);
            $notificationController->hideFromAllUsersExcept($notificationId, $playerIds);
        }

        $controller->delete($id);
        header("Location: manage.php?success=1");
        exit;
    }
    if ($action === 'modifier' && $id) {
        header("Location: edit.php?id=$id");
        exit;
    }
    header("Location: manage.php");
    exit;
}

if (isset($_GET['success']) && $_GET['success'] === '1') {
    $message = 'Le match a bien été supprimé et les joueurs ont été prévenus.';
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

    <?php if (!empty($message)): ?>
        <div class="form-message form-message--success auto-dismiss manage-form-message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <main class="matchs-page">
        <?php if (empty($slots)): ?>

            <div class="manage-header">
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
                    <div class="match-card" data-timeslot="<?= $slot->getId() ?>" data-location="<?= htmlspecialchars($slot->getLocation(), ENT_QUOTES) ?>">
                        <p class="match-date">
                            <?= $slot->getFormattedDate() ?> – <?= $slot->getFormattedTime() ?>
                        </p>
                        <div class="match-info">
                            <img src="Images/time.png" alt="Durée">
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
                            <img src="Images/price.png" alt="Prix">
                            <span><?= $slot->getFormattedPrice() ?></span>
                        </div>

                        <div class="match-info">
                            <img src="Images/level.png" alt="Niveau">
                            <span>Niveau : <?= $slot->getLevel() ?></span>
                        </div>

                        <form method="POST" style="display:flex; gap:8px; margin-top:8px">
                            <input type="hidden" name="id" value="<?= $slot->getId() ?>">
                            <button name="action" value="modifier" class="btn-primary btn-manage-action" style="flex:1;">
                                Modifier
                            </button>
                            <button name="action" value="supprimer" class="btn-secondary btn-manage-action" style="flex:1;">
                                Supprimer
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </main>

    <div class="modal-overlay" id="card-action-modal">
        <div class="modal-content">
            <button class="modal-close" id="modal-close">×</button>
            <h3>Que voulez-vous faire ?</h3>
            <div class="modal-actions">
                <button type="button" class="btn-primary" id="modal-players-button">Voir les joueurs</button>
                <button type="button" class="btn-secondary" id="modal-map-button">Voir le lieu</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="modal-cancel">Fermer</button>
            </div>
        </div>
    </div>

    <?php require "footer.php"; ?>
</body>

</html>