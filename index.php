<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="PadelConnect vous aide à trouver des partenaires compatibles, réserver un terrain en quelques secondes et profiter d'un jeu fluide, sans prise de tête.">
    <title>PadelConnect – Jouez au padel quand vous voulez</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="./Images/favicon.ico" type="image/x-icon">
</head>
<!-- PHP if pour change de page si connetion -->

<body>
    <header class="navbar">
        <div class="nav-left">
            <img src="./Images/logoW.png" alt="Logo PadelConnect" class="logo">
            <h1>PadelConnect</h1>
        </div>
        <div class="nav-right">
            <a href="#">Se connecter /</a>
            <a href="#"> S'inscrire</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-text">
            <h2>Jouez au padel quand vous voulez,<br> avec qui vous voulez</h2>
            <p>PadelConnect vous aide à trouver des partenaires compatibles, réserver un terrain en quelques secondes et
                profiter d'un jeu fluide, sans prise de tête.</p>
            <div class="hero-img">
                <img src="./Images/padelraquette.png" alt="Padel raquette">
            </div>

            <a href="#" class="btn-primary hero-btn">S'inscrire gratuitement</a>
            <p class="micro-text">Aucun engagement<br>Disponible partout en France</p>
        </div>
    </section>

    <section class="steps">
        <h3>Comment ça marche</h3>

        <div class="steps-body">
            <div class="steps-grid">

                <div class="step-card">
                    <img src="./Images/matcher.png" class="icon" alt="Matcher">
                    <div>
                        <h4>Matcher</h4>
                        <p>Trouvez des partenaires compatibles</p>
                    </div>
                </div>

                <div class="step-card">
                    <img src="./Images/calendar.png" class="icon" alt="Calendrier">
                    <div>
                        <h4>Réserver</h4>
                        <p>Réservez un terrain en quelques secondes</p>
                    </div>
                </div>

                <div class="step-card">
                    <img src="./Images/smile.png" class="icon" alt="S'amuser">
                    <div>
                        <h4>S'amuser</h4>
                        <p>Profitez d'un match fluide, équilibré et sans prise de tête</p>
                    </div>
                </div>

            </div>

            <div class="steps-img">
                <img src="./Images/padelterrain.png" alt="Joueurs de padel">
            </div>
        </div>
    </section>

    <section class="advantages">
        <h3>Avantages</h3>

        <div class="adv-grid">

            <div class="adv-card">
                <img src="./Images/equilibre.png" class="icon" alt="Équilibre">
                <h4>Matchs équilibrés</h4>
                <p>grâce à un système de niveau</p>
            </div>

            <div class="adv-card">
                <img src="./Images/reservation.png" class="icon" alt="Réservation">
                <h4>Réservation simplifiée</h4>
                <p>pour réserver un terrain sans prise de tête</p>
            </div>

            <div class="adv-card">
                <img src="./Images/notification.png" class="icon" alt="Notification">
                <h4>Notifications intelligentes</h4>
                <p>pour vous proposer des matchs disponibles</p>
            </div>

        </div>

        <a href="#" class="btn-primary adv-btn">S'inscrire</a>
    </section>

    <a href="#" class="mod"></a>

    <a href="#" class="back-to-top" aria-label="Retour en haut">
        <img src="./Images/fleche.png" alt="Flèche vers le haut">
    </a>

    <?php require "footer.php" ?>

</body>

</html>