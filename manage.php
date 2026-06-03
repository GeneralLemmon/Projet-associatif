<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemId = $_POST['itemId'];
    $action = $_POST['action'];

    switch ($action) {
        case 'modifier':
            header("Location: edit.php?id=" . $itemId);
            exit();
            break;

        case 'supprimer':
            $messageSuppression = "L'élément n°" . $itemId . " a bien été supprimé.";
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
    <img src="images/logoW.png" class="logo" alt="Logo PadelConnect" />
</head>

<body>

    <?php require "navbar.php"; ?>

    <h2>Gérer mes matchs</h2>

    <?php if (isset($messageSuppression)): ?>
        <p style="color: green; font-weight: bold; text-align: center;"><?= $messageSuppression ?></p>
    <?php endif; ?>

    <div class="matchs-container">

        <div class="match-card">
            <p>14 juin 2026 - 18h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Puteaux Île</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 2/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 3</p>

            <form action="" method="POST">
                <input type="hidden" name="itemId" value="1">
                <button type="submit" name="action" value="modifier" class="btn-primary">Modifier</button>
                <button type="submit" name="action" value="supprimer" class="btn-secondary">Supprimer</button>
            </form>
        </div>

        <div class="match-card">
            <p>8 juillet 2026 - 13h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Forest Hill la Défense</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 4/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 3</p>

            <form action="" method="POST">
                <input type="hidden" name="itemId" value="2">
                <button type="submit" name="action" value="modifier" class="btn-primary">Modifier</button>
                <button type="submit" name="action" value="supprimer" class="btn-secondary">Supprimer</button>
            </form>
        </div>

        <div class="match-card">
            <p>14 juin 2026 - 18h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Sportfield la Défense</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 1/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 4</p>

            <form action="" method="POST">
                <input type="hidden" name="itemId" value="3">
                <button type="submit" name="action" value="modifier" class="btn-primary">Modifier</button>
                <button type="submit" name="action" value="supprimer" class="btn-secondary">Supprimer</button>
            </form>
        </div>

    </div>


    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>