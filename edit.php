<?php
$itemId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$itemId) {
    header("Location: manage.php");
    exit();
}

$currentDate  = ""; 
$currentTime  = ""; 
$currentLevel = ""; 
$currentVenue = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedDate  = isset($_POST['date']) ? $_POST['date'] : '';
    $updatedTime  = isset($_POST['heure']) ? $_POST['heure'] : '';
    $updatedLevel = isset($_POST['niveau']) ? $_POST['niveau'] : '';
    $updatedVenue = isset($_POST['lieu']) ? $_POST['lieu'] : '';

    header("Location: manage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Modifier le match</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <form action="edit.php?id=<?php echo htmlspecialchars($itemId); ?>" method="POST">

        <label for="date">Date </label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($currentDate); ?>">
        <br><br>

        <label for="heure">Heure </label>
        <input type="time" id="heure" name="heure" value="<?php echo htmlspecialchars($currentTime); ?>">
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
        <input type="text" id="lieu" name="lieu" value="<?php echo htmlspecialchars($currentVenue); ?>" required>
        <br><br>

        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
    </form>

    <?php require "footer.php"; ?>
</body>

</html>