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
                            <button name="action" value="modifier" class="btn-primary btn-manage-action" style="flex:1;">
                                Modifier
                            </button>
                            <button name="action" value="supprimer" class="btn-secondary btn-manage-action" style="flex:1;"
                                onclick="return confirm('Supprimer ce match ?')">
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

    <script>
    const cardModal = document.getElementById('card-action-modal');
    const modalClose = document.getElementById('modal-close');
    const modalCancel = document.getElementById('modal-cancel');
    const modalPlayersButton = document.getElementById('modal-players-button');
    const modalMapButton = document.getElementById('modal-map-button');

    document.querySelectorAll('.match-card').forEach(card => {
        card.addEventListener('click', event => {
            if (event.target.closest('form') || event.target.closest('a') || event.target.closest('button')) {
                return;
            }
            const timeslotId = card.dataset.timeslot;
            const location = card.dataset.location || 'Lieu inconnu';
            modalPlayersButton.dataset.timeslot = timeslotId;
            modalMapButton.dataset.location = location;
            cardModal.classList.add('open');
        });
    });

    function closeCardModal() {
        cardModal.classList.remove('open');
    }

    [modalClose, modalCancel].forEach(button => button.addEventListener('click', closeCardModal));

    cardModal.addEventListener('click', event => {
        if (event.target === cardModal) closeCardModal();
    });

    modalPlayersButton.addEventListener('click', () => {
        const id = modalPlayersButton.dataset.timeslot;
        if (id) {
            window.location.href = `players.php?id=${encodeURIComponent(id)}`;
        }
    });

    modalMapButton.addEventListener('click', () => {
        const location = modalMapButton.dataset.location;
        if (location) {
            const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`;
            window.open(url, '_blank');
        }
    });
    </script>
</body>

</html>