<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
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
                        <div class="match-card">
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
</body>

</html>