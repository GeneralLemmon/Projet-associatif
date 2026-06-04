<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$controller = new TimeSlotController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['lieu']   ?? '';
    $date     = $_POST['date']   ?? '';
    $time     = $_POST['heure']  ?? '';
    $level    = (int)($_POST['niveau'] ?? 1);
    $duration = (int)($_POST['duree'] ?? 90);
    
    if ($location && $date && $time) {
        $controller->create($location, $date, $time, $level, $duration);
    }
    header("Location: manage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Créer un match – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="profil-page">
        <h2 class="profil-title">Créer un nouveau match</h2>

        <div class="card">
            <form action="create.php" method="POST">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="form-group">
                        <label for="heure">Heure</label>
                        <input type="time" id="heure" name="heure" required>
                    </div>
                    <div class="form-group">
                        <label for="duree">Durée</label>
                        <select id="duree" name="duree">
                            <option value="60">1h</option>
                            <option value="90" selected>1h30</option>
                            <option value="120">2h</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="lieu">Lieu</label>
                        <select id="lieu" name="lieu">
                            <option value="Puteaux Île">Puteaux Île</option>
                            <option value="Forest Hill la Défense">Forest Hill la Défense</option>
                            <option value="Sportfield la Défense">Sportfield la Défense</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="niveau">Niveau requis</label>
                        <select id="niveau" name="niveau">
                            <option value="1">1 – Débutant</option>
                            <option value="2">2 – Perfectionnement</option>
                            <option value="3">3 – Élémentaire</option>
                            <option value="4">4 – Intermédiaire</option>
                            <option value="5">5 – Confirmé</option>
                            <option value="6">6 – Avancé</option>
                            <option value="7">7 – Expert</option>
                            <option value="8">8 – Élite</option>
                        </select>
                    </div>

                </div>

                <div class="save-bar">
                    <a href="manage.php" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Créer le match</button>
                </div>
            </form>
        </div>
    </main>

    <?php require "footer.php"; ?>
</body>

</html>