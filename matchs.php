<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php require "navbar.php"; ?>

    <h2>Mes prochains matchs</h2>

    <br><br>
    <div class="matchs-container">
        <div class="match-card">
            <p>14 juin 2026 - 18h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Puteaux Île</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 2/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 3</p>
        </div>
        <br><br>
        <div class="match-card">
            <p>8 juillet 2026 - 13h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Forest Hill la Défense</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 4/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 3</p>
        </div>
        <br><br>
        <div class="match-card">
            <p>14 juin 2026 - 18h</p>
            <img src="Images/lieu.png" alt="Lieu" width="50">
            <p> Sportfield la Défense</p>
            <img src="Images/player.png" alt="Joueurs" width="50">
            <p> 1/4 Joueurs</p>
            <img src="Images/level.png" alt="Niveau" width="50">
            <p> Niveau : 4</p>
        </div>
    </div>

    <img src="Images/notification.png" alt="Logo Notification" class="logo" width="50">
    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>