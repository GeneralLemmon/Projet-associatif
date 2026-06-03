<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newDate  = isset($_POST['date']) ? $_POST['date'] : '';
    $newTime  = isset($_POST['heure']) ? $_POST['heure'] : '';
    $newLevel = isset($_POST['niveau']) ? $_POST['niveau'] : '';
    $newVenue = isset($_POST['lieu']) ? $_POST['lieu'] : '';

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