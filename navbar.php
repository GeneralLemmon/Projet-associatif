<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$isConnected = isset($_SESSION['user']);
$isAdmin     = $isConnected && !empty($_SESSION['user']['is_admin']);

$notifCount = 0;
if ($isConnected) {
    require_once __DIR__ . "/autoload.php";

    $db = Database::getInstance()->getConnection();

    // Cleanup
    $db->exec("DELETE FROM notification WHERE created_at < NOW() - INTERVAL 24 HOUR");
    $db->exec("DELETE FROM timeslot WHERE CONCAT(date, ' ', time) < NOW() - INTERVAL 72 HOUR");

    $notifController = new NotificationController();
    $notifs = $notifController->getForUser(
        $_SESSION['user']['id'],
        $_SESSION['user']['level'] ?? 'Débutant'
    );
    $notifCount = count($notifs);
}
?>

<header class="navbar">
    <div class="nav-left">
        <a href="index.php" class="brand">
            <img src="./Images/logoW.png" alt="Logo PadelConnect" class="logo">
            <h1>PadelConnect</h1>
        </a>
    </div>

    <?php if ($isConnected): ?>
        <div class="nav-center">
            <a href="search.php">Chercher un match</a>
            <a href="match.php">Mes matchs</a>
            <?php if ($isAdmin): ?>
                <a href="manage.php">Gérer</a>
                <a href="notify.php">Notifier</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="nav-right">
        <?php if ($isConnected): ?>
            <!-- CLOCHE -->
            <button class="notif-bell" id="notif-bell-btn" aria-label="Notifications">
                <img src="./Images/notification.png" alt="Notifications">
                <?php if ($notifCount > 0): ?>
                    <span class="notif-badge"><?= $notifCount ?></span>
                <?php endif; ?>
            </button>
            <div class="theme-toggle-btn theme-btn-inline">
                <img class="theme-icon" src="./Images/moon.png" alt="Changer de thème">
            </div>
            <a href="profile.php">
                <img src="./Images/profilL.png" alt="Profil" class="profile-icon">
                <?= htmlspecialchars($_SESSION['user']['firstName']) ?>
            </a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
            <a href="signup.php">S'inscrire</a>
        <?php endif; ?>
    </div>
</header>

<?php if ($isConnected): ?>
    <!-- OVERLAY NOTIFICATIONS -->
    <div class="notif-overlay" id="notif-overlay">
        <div class="notif-panel">
            <div class="notif-panel-header">
                <h3>Notifications</h3>
                <button id="notif-close">×</button>
            </div>

            <div class="notif-panel-body">
                <?php if (empty($notifs)): ?>
                    <p class="notif-empty">Aucune nouvelle notification.</p>
                <?php else: ?>
                    <?php foreach ($notifs as $n): ?>
                        <div class="notif-item" data-id="<?= $n['id_notification'] ?>">
                            <p class="notif-msg"><?= htmlspecialchars($n['message']) ?></p>
                            <div class="notif-meta">
                                <span class="notif-time">
                                    <?= (new DateTime($n['created_at']))->format('d/m/Y H:i') ?>
                                    <?= $n['level'] ? ' · Niveau ' . $n['level'] : ' · Tous niveaux' ?>
                                </span>
                                <form method="POST" action="mark_read.php" class="notif-mark-read-form">
                                    <input type="hidden" name="notification_id" value="<?= $n['id_notification'] ?>">
                                    <button type="button" class="btn-secondary notif-mark-read-btn">Marquer lu</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($notifs)): ?>
                <div class="notif-panel-footer">
                    <form method="POST" action="mark_read.php">
                        <button type="submit" class="btn-secondary" style="width:100%">
                            Tout marquer comme lu
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>