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

    <main>
        <h2>Chercher un match</h2>

        <div class="match-results">
            <div class="match-card">
                <h3>Match 1</h3>
                <p>Lieu : Club de Padel XYZ</p>
                <p>Date : 15 juin 2024</p>
                <p>Heure : 18h00</p>
                <button class="join-btn">Rejoindre</button>
            </div>
        </div>
    </main>

    <?php require "footer.php"; ?>
    <script src="script.js"></script>
</body>

</html>