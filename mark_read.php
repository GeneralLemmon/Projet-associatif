<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/autoload.php";
$ctrl = new NotificationController();
$ctrl->markAllRead($_SESSION['user']['id'], $_SESSION['user']['level'] ?? 'Débutant');

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
