<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

spl_autoload_register(fn($c) => require "$c.php");
$controller = new TimeSlotController();
$userId     = $_SESSION['user']['id'];

// Action : quitter un match
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_id'])) {
    $controller->leave($userId, (int)$_POST['leave_id']);
    header("Location: match.php");
    exit;
}

$slots = $controller->getMyMatches($userId);
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes matchs – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="./Images/favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">
            Bonjour <?= htmlspecialchars($_SESSION['user']['firstName']) ?>
        </h2>

        <section class="matchs-section">
            <h3 class="matchs-title">Mes prochains matchs</h3>

            <?php if (empty($slots)): ?>
                <p style="color:var(--text-soft)">
                    Vous n'êtes inscrit à aucun match.
                    <a href="search.php" style="color:var(--blue)">Chercher un match →</a>
                </p>
            <?php else: ?>
                <div class="matchs-container">
                    <?php foreach ($slots as $i => $slot): ?>
                        <div class="match-card <?= $i === 0 ? 'match-card--active' : '' ?>">
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

                            <div class="match-info">
                                <img src="Images/player.png" alt="Joueurs">
                                <span><?= $slot->getPlayerCount() ?>/4 Joueurs</span>
                            </div>

                            <div class="match-info">
                                <img src="Images/level.png" alt="Niveau">
                                <span>Niveau : <?= $slot->getLevel() ?></span>
                            </div>

                            <form method="POST" style="margin-top:8px">
                                <input type="hidden" name="leave_id" value="<?= $slot->getId() ?>">
                                <button type="submit" class="<?= $i === 0 ? 'btn-leave-active' : 'btn-secondary' ?>"
                                    onclick="return confirm('Quitter ce match ?')"
                                    style="width:100%; padding:10px">
                                    Quitter
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