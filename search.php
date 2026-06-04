<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

spl_autoload_register(fn($c) => require "$c.php");
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
$allSlots = $controller->readAll();
echo "<pre>Tous les matchs : " . count($allSlots) . "\n";
foreach ($allSlots as $s) {
    echo $s->getDate() . " " . $s->getTime() . " – joueurs: " . $s->getPlayerCount() . "\n";
}
echo "Matchs disponibles pour user $userId : " . count($slots) . "</pre>";
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