<?php
session_start();

$itemId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$itemId || !isset($_SESSION['matches'][$itemId])) {
    header("Location: manage.php");
    exit();
}

$currentMatch = $_SESSION['matches'][$itemId];
$currentDate  = $currentMatch['date']; 
$currentTime  = $currentMatch['time']; 
$currentLevel = $currentMatch['level']; 
$currentVenue = $currentMatch['venue']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedDate  = isset($_POST['date']) ? $_POST['date'] : '';
    $updatedTime  = isset($_POST['heure']) ? $_POST['heure'] : '';
    $updatedLevel = isset($_POST['niveau']) ? $_POST['niveau'] : '';
    $updatedVenue = isset($_POST['lieu']) ? $_POST['lieu'] : '';

    // Mise à jour des valeurs dans la session globale
    $_SESSION['matches'][$itemId]['date'] = $updatedDate;
    $_SESSION['matches'][$itemId]['time'] = $updatedTime;
    $_SESSION['matches'][$itemId]['level'] = $updatedLevel;
    $_SESSION['matches'][$itemId]['venue'] = $updatedVenue;

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

    <h2>Modifier le match n°<?= htmlspecialchars($itemId) ?></h2>

    <form action="edit.php?id=<?php echo htmlspecialchars($itemId); ?>" method="POST">

        <label for="date">Date </label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($currentDate); ?>">
        <br><br>

        <label for="heure">Heure </label>
        <input type="time" id="heure" name="heure" value="<?php echo htmlspecialchars($currentTime); ?>">
        <br><br>

        <label for="niveau">Niveau :</label>
        <select id="niveau" name="niveau">
            <option value="1" <?= $currentLevel == '1' ? 'selected' : '' ?>>1 - Débutant</option>
            <option value="2" <?= $currentLevel == '2' ? 'selected' : '' ?>>2 - Perfectionnement</option>
            <option value="3" <?= $currentLevel == '3' ? 'selected' : '' ?>>3 - Élémentaire</option>
            <option value="4" <?= $currentLevel == '4' ? 'selected' : '' ?>>4 - Intermédiaire</option>
            <option value="5" <?= $currentLevel == '5' ? 'selected' : '' ?>>5 - Confirmé</option>
            <option value="6" <?= $currentLevel == '6' ? 'selected' : '' ?>>6 - Avancé</option>
            <option value="7" <?= $currentLevel == '7' ? 'selected' : '' ?>>7 - Expert</option>
            <option value="8" <?= $currentLevel == '8' ? 'selected' : '' ?>>8 - Professionnel</option>
        </select>
        <br><br>

        <label for="lieu">Lieu :</label>
        <select id="lieu" name="lieu">
            <option value="Puteaux Île" <?= $currentVenue == 'Puteaux Île' ? 'selected' : '' ?>>Puteaux Île</option>
            <option value="Forest Hill la Défense" <?= $currentVenue == 'Forest Hill la Défense' ? 'selected' : '' ?>>Forest Hill la Défense</option>
            <option value="Sportfield la Défense" <?= $currentVenue == 'Sportfield la Défense' ? 'selected' : '' ?>>Sportfield la Défense</option>
        </select>
        <br><br>

        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
    </form>

    <?php require "footer.php"; ?>
</body>

</html>
