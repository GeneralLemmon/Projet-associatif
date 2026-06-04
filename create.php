<?php
session_start();

// Sécurité si jamais la session n'est pas encore initialisée
if (!isset($_SESSION['matches'])) {
    $_SESSION['matches'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newDate  = isset($_POST['date']) ? $_POST['date'] : '';
    $newTime  = isset($_POST['heure']) ? $_POST['heure'] : '';
    $newLevel = isset($_POST['niveau']) ? $_POST['niveau'] : '';
    $newVenue = isset($_POST['lieu']) ? $_POST['lieu'] : '';

    if (!empty($newDate) && !empty($newTime)) {
        // Trouve le plus grand ID existant pour créer le suivant (ex: 4)
        $newId = empty($_SESSION['matches']) ? 1 : max(array_keys($_SESSION['matches'])) + 1;

        // On ajoute le nouveau match dans la session
        $_SESSION['matches'][$newId] = [
            'date' => $newDate,
            'time' => $newTime,
            'venue' => $newVenue,
            'players' => '1/4 Joueurs', // Valeur fictive par défaut
            'level' => $newLevel
        ];
    }

    // Une fois ajouté, on retourne instantanément à l'écran d'accueil
    header("Location: manage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Créer un match</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <h2>Créer un nouveau match</h2>

    <form action="create.php" method="POST">

        <label for="date">Date </label>
        <input type="date" id="date" name="date" required>
        <br><br>

        <label for="heure">Heure </label>
        <input type="time" id="heure" name="heure" required>
        <br><br>

        <label for="niveau">Niveau :</label>
        <select id="niveau" name="niveau">
            <option value="1">1 - Débutant</option>
            <option value="2">2 - Perfectionnement</option>
            <option value="3">3 - Élémentaire</option>
            <option value="4">4 - Intermédiaire</option>
            <option value="5">5 - Confirmé</option>
            <option value="6">6 - Avancé</option>
            <option value="7">7 - Expert</option>
            <option value="8">8 - Professionnel</option>
        </select>
        <br><br>

        <label for="lieu">Lieu :</label>
        <select id="lieu" name="lieu">
            <option value="Puteaux Île">Puteaux Île</option>
            <option value="Forest Hill la Défense">Forest Hill la Défense</option>
            <option value="Sportfield la Défense">Sportfield la Défense</option>
        </select>
        <br><br>

        <button type="submit" class="btn-primary">Créer le match</button>
    </form>

    <?php require "footer.php"; ?>
</body>

</html>