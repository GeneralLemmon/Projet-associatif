<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$ctrl = new NotificationController();

if (!empty($_POST['notification_id'])) {
    $notificationId = (int)$_POST['notification_id'];
    if ($notificationId > 0) {
        $ctrl->markRead($_SESSION['user']['id'], $notificationId);
    }
} else {
    $ctrl->markAllRead($_SESSION['user']['id'], $_SESSION['user']['level'] ?? 'Débutant');
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
