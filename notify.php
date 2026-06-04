<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header("Location: index.php"); exit;
}

require_once __DIR__ . "/autoload.php";
$ctrl = new NotificationController();
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $level   = $_POST['level'] !== '' ? (int)$_POST['level'] : null;
    if ($message) {
        $ctrl->create($message, $level);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8">
    <title>Envoyer une notification – PadelConnect</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require "navbar.php"; ?>

    <main class="profil-page">
        <h2 class="profil-title">Envoyer une notification</h2>

        <?php if ($success): ?>
            <p class="form-message form-message--success">Notification envoyée !</p>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4"
                            placeholder="Ex : Des créneaux sont disponibles ce week-end !" required></textarea>
                    </div>

                    <div class="form-group full">
                        <label for="level">Destinataires</label>
                        <select id="level" name="level">
                            <option value="">Tous les joueurs</option>
                            <option value="1">Niveau 1 – Débutant</option>
                            <option value="2">Niveau 2 – Perfectionnement</option>
                            <option value="3">Niveau 3 – Élémentaire</option>
                            <option value="4">Niveau 4 – Intermédiaire</option>
                            <option value="5">Niveau 5 – Confirmé</option>
                            <option value="6">Niveau 6 – Avancé</option>
                            <option value="7">Niveau 7 – Expert</option>
                            <option value="8">Niveau 8 – Élite</option>
                        </select>
                    </div>
                </div>

                <div class="save-bar">
                    <button type="submit" class="btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </main>

    <?php require "footer.php"; ?>
</body>
</html>