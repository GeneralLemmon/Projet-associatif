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

    <?php require "navbar.php"; ?>

    <main class="matchs-page">

        <h2 class="matchs-greeting">Bonjour [Nom de l'utilisateur]</h2> <!-- Remplacer par le prénom de l'utilisateur avec PHP -->

        <section class="matchs-section">
            <h3 class="matchs-title">Mes prochains matchs</h3>

            <div class="matchs-container">

                <!-- Carte active -->
                <div class="match-card match-card--active">
                    <p class="match-date">14 juin 2026 - 18h</p>
                    <div class="match-info">
                        <img src="Images/lieu.png" alt="Lieu">
                        <span>Puteaux Île</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/player.png" alt="Joueurs">
                        <span>4/4 Joueurs</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/level.png" alt="Niveau">
                        <span>Niveau moyen : 3</span>
                    </div>
                </div>

                <!-- Carte normale -->
                <div class="match-card">
                    <p class="match-date">18 juillet 2026 - 8h</p>
                    <div class="match-info">
                        <img src="Images/lieu.png" alt="Lieu">
                        <span>Forest Hill la Défense</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/player.png" alt="Joueurs">
                        <span>3/4 Joueurs</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/level.png" alt="Niveau">
                        <span>Niveau moyen : 3</span>
                    </div>
                </div>

                <!-- Carte normale -->
                <div class="match-card">
                    <p class="match-date">18 juillet 2026 - 8h</p>
                    <div class="match-info">
                        <img src="Images/lieu.png" alt="Lieu">
                        <span>Sportfield la Défense</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/player.png" alt="Joueurs">
                        <span>3/4 Joueurs</span>
                    </div>
                    <div class="match-info">
                        <img src="Images/level.png" alt="Niveau">
                        <span>Niveau moyen : 4</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <a href="#" class="notif-btn" aria-label="Notifications">
        <img src="Images/notification.png" alt="Notifications">
    </a>

    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>