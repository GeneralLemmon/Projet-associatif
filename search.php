<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/autoload.php";

function normalizeLevel($level): int
{
    if (is_numeric($level)) {
        return (int) $level;
    }

    $mapping = [
        'Débutant' => 1,
        'Perfectionnement' => 2,
        'Élémentaire' => 3,
        'Intermédiaire' => 4,
        'Confirmé' => 5,
        'Avancé' => 6,
        'Expert' => 7,
        'Élite' => 8,
    ];

    return $mapping[$level] ?? 0;
}

$controller = new TimeSlotController();
$userId     = $_SESSION['user']['id'];
$message    = '';

// Action : rejoindre un match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_id'])) {
    $joinId = (int)$_POST['join_id'];
    $ok = $controller->join($userId, $joinId);
    $message = $ok
        ? 'success:Vous avez rejoint le match !'
        : 'error:Ce match est déjà complet.';
}

$slots = $controller->getAvailable($userId);

if (empty($_SESSION['user']['is_admin'])) {
    $userLevel = normalizeLevel($_SESSION['user']['level'] ?? '');
    $minMatchLevel = isset($_SESSION['user']['minLevel']) ? (int)$_SESSION['user']['minLevel'] : 1;
    $slots = array_filter($slots, function ($slot) use ($userLevel, $minMatchLevel) {
        $slotLevel = normalizeLevel($slot->getLevel());
        return $slotLevel <= $userLevel && $slotLevel >= $minMatchLevel;
    });
}

$dateFilter = $_GET['date'] ?? null;

if ($dateFilter) {
    $slots = array_filter($slots, fn($s) => $s->getDate() === $dateFilter);
}

$allSlots = $controller->readAll();
[$msgType, $msgText] = $message ? explode(':', $message, 2) : ['', ''];
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chercher un match – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="matchs-page">
        <h2 class="matchs-greeting">Chercher un match</h2>

        <?php if ($msgText): ?>
            <p class="form-message form-message--<?= $msgType ?>">
                <?= htmlspecialchars($msgText) ?>
            </p>
        <?php endif; ?>

        <section class="matchs-section">
            <h3 class="matchs-title">Matchs disponibles</h3>
            <form method="GET" class="search-bar" style="margin-bottom: 20px;">
                <div class="search-field">
                    <label for="date">Date du match</label>
                    <input type="date" id="date" name="date" value="<?= $_GET['date'] ?? '' ?>">
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 10px;">
                    Rechercher
                </button>
            </form>


            <?php if (empty($slots)): ?>
                <p style="color:var(--text-soft)">
                    Aucun match disponible pour le moment.
                </p>
            <?php else: ?>
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
                                <img src="Images/level.png" alt="Niveau">
                                <span>Niveau : <?= $slot->getLevel() ?></span>
                            </div>

                            <form method="POST" style="margin-top:8px">

                                <input type="hidden" name="join_id" value="<?= $slot->getId() ?>">
                                <button type="submit" class="btn-primary join-btn">
                                    Rejoindre
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php require "footer.php"; ?>

    <div class="modal-overlay" id="card-action-modal">
        <div class="modal-content">
            <button class="modal-close" id="modal-close">×</button>
            <h3>Détails du match</h3>
            <div class="modal-actions">
                <button type="button" class="btn-primary" id="modal-players-button">Voir les joueurs</button>
                <button type="button" class="btn-secondary" id="modal-map-button">Voir le lieu</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="modal-cancel">Fermer</button>
            </div>
        </div>
    </div>

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