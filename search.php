<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadelConnect</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="./Images/favicon.ico" type="image/x-icon">
</head>

<body>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require "navbar.php"; ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">Chercher un match</h2>

        <section class="matchs-section">
            <h3 class="matchs-title">Prochain Matchs</h3>

            <div class="matchs-container">

                <div class="match-card">
                    <p class="match-date">15 juin 2024 - 18h00</p>

                    <div class="match-info">
                        <img src="Images/lieu.png" alt="Lieu">
                        <span>Club de Padel XYZ</span>
                    </div>

                    <div class="match-info">
                        <img src="Images/player.png" alt="Joueurs">
                        <span>2/4 Joueurs</span>
                    </div>

                    <div class="match-info">
                        <img src="Images/level.png" alt="Niveau">
                        <span>Niveau moyen : 3</span>
                    </div>

                    <button class="join-btn">Rejoindre</button>
                </div>

            </div>
        </section>
    </main>

    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>