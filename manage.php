<?php
session_start();

if (!isset($_SESSION['matches'])) {
    $_SESSION['matches'] = [
        1 => [
            'date' => '2026-06-14',
            'time' => '18:00',
            'venue' => 'Puteaux Île',
            'players' => '2/4 Joueurs',
            'level' => '3'
        ],
        2 => [
            'date' => '2026-07-08',
            'time' => '13:00',
            'venue' => 'Forest Hill la Défense',
            'players' => '4/4 Joueurs',
            'level' => '3'
        ],
        3 => [
            'date' => '2026-06-14',
            'time' => '18:00',
            'venue' => 'Sportfield la Défense',
            'players' => '1/4 Joueurs',
            'level' => '4'
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemId = $_POST['itemId'];
    $action = $_POST['action'];

    switch ($action) {
        case 'modifier':
            header("Location: edit.php?id=" . $itemId);
            exit();
            break;

        case 'supprimer':
            if (isset($_SESSION['matches'][$itemId])) {
                unset($_SESSION['matches'][$itemId]);
                $messageSuppression = "L'élément n°" . $itemId . " a bien été supprimé.";
            }
            break;

        default:
            $messageSuppression = "Action non autorisée ou inconnue.";
            break;
    }   
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <img src="images/logoW.png" class="logo" alt="Logo PadelConnect" />

    <?php require "navbar.php"; ?>

    <h2>Gérer mes matchs</h2>

    <div style="text-align: center; margin-bottom: 20px;">
        <a href="create.php" class="btn-primary" style="text-decoration: none; padding: 10px 20px; display: inline-block;">+ Créer un match</a>
    </div>

    <?php if (isset($messageSuppression)): ?>
        <p style="color: green; font-weight: bold; text-align: center;"><?= htmlspecialchars($messageSuppression) ?></p>
    <?php endif; ?>

    <div class="matchs-container">

        <?php if (empty($_SESSION['matches'])): ?>
            <p style="text-align: center;">Aucun match disponible.</p>
        <?php else: ?>
            <?php foreach ($_SESSION['matches'] as $id => $match): ?>
                <div class="match-card">
                    <p><?= htmlspecialchars($match['date']) ?> - <?= htmlspecialchars($match['time']) ?></p>
                    <img src="Images/lieu.png" alt="Lieu" width="50">
                    <p> <?= htmlspecialchars($match['venue']) ?></p>
                    <img src="Images/player.png" alt="Joueurs" width="50">
                    <p> <?= htmlspecialchars($match['players'] ?? '0/4 Joueurs') ?></p>
                    <img src="Images/level.png" alt="Niveau" width="50">
                    <p> Niveau : <?= htmlspecialchars($match['level']) ?></p>

                    <form action="" method="POST">
                        <input type="hidden" name="itemId" value="<?= $id ?>">
                        <button type="submit" name="action" value="modifier" class="btn-primary">Modifier</button>
                        <button type="submit" name="action" value="supprimer" class="btn-secondary">Supprimer</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>