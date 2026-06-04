<?php
function runCleanup(PDO $db): void
{
    // Supprimer les notifications de plus de 24h
    $db->exec("DELETE FROM notification WHERE created_at < NOW() - INTERVAL 24 HOUR");

    // Supprimer les timeslots de plus de 72h (date+time dépassés depuis 72h)
    $db->exec("DELETE FROM timeslot 
               WHERE CONCAT(date, ' ', time) < NOW() - INTERVAL 72 HOUR");
}