<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php");
    exit;
}

spl_autoload_register(fn($c) => require "$c.php");
$controller = new TimeSlotController();

$id = (int)($_GET['id'] ?? 0);
$slot = $controller->read($id);

if (!$slot) {
    header("Location: manage.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['lieu']   ?? '';
    $date     = $_POST['date']   ?? '';
    $time     = $_POST['heure']  ?? '';
    $level    = (int)($_POST['niveau'] ?? 1);
    $duration = (int)($_POST['duree'] ?? 90);
    
    $controller->update($id, $location, $date, $time, $level, $duration);
    header("Location: manage.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Modifier le match – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php require "navbar.php"; ?>

    <main class="profil-page">
        <h2 class="profil-title">Modifier le match n°<?= $id ?></h2>

        <div class="card">
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date"
                            value="<?= htmlspecialchars($slot->getDate()) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="heure">Heure</label>
                        <input type="time" id="heure" name="heure"
                            value="<?= htmlspecialchars($slot->getTime()) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="duree">Durée</label>
                        <select id="duree" name="duree">
                            <option value="60" <?= $slot->getDuration() === 60  ? 'selected' : '' ?>>1h</option>
                            <option value="90" <?= $slot->getDuration() === 90  ? 'selected' : '' ?>>1h30</option>
                            <option value="120" <?= $slot->getDuration() === 120 ? 'selected' : '' ?>>2h</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label for="lieu">Lieu</label>
                        <select id="lieu" name="lieu">
                            <?php foreach (['Puteaux Île', 'Forest Hill la Défense', 'Sportfield la Défense'] as $lieu): ?>
                                <option value="<?= $lieu ?>" <?= $slot->getLocation() === $lieu ? 'selected' : '' ?>>
                                    <?= $lieu ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label for="niveau">Niveau requis</label>
                        <select id="niveau" name="niveau">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= $slot->getLevel() === $i ? 'selected' : '' ?>>
                                    <?= $i ?> – Niveau <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                </div>

                <div class="save-bar">
                    <a href="manage.php" class="btn-secondary">Annuler</a>
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </main>

    <?php require "footer.php"; ?>
</body>

</html>