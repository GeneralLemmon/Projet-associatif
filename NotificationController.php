<?php
class NotificationController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une notification (admin)
    public function create(string $message, ?int $level = null): int
    {
        $req = $this->db->prepare(
            "INSERT INTO notification (message, level) VALUES (?, ?)"
        );
        $req->execute([$message, $level]);

        $id = (int)$this->db->lastInsertId();
        if ($id === 0) {
            $req = $this->db->prepare(
                "SELECT id_notification FROM notification WHERE message = ? ORDER BY created_at DESC LIMIT 1"
            );
            $req->execute([$message]);
            $id = (int)$req->fetchColumn();
        }

        return $id;
    }

    public function hideFromAllUsersExcept(int $notificationId, array $allowedUserIds): void
    {
        if (empty($allowedUserIds)) {
            $req = $this->db->prepare(
                "INSERT IGNORE INTO notification_read (id_user, id_notification)
                 SELECT id_user, ? FROM `user`"
            );
            $req->execute([$notificationId]);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($allowedUserIds), '?'));
        $req = $this->db->prepare(
            "INSERT IGNORE INTO notification_read (id_user, id_notification)
             SELECT id_user, ? FROM `user` WHERE id_user NOT IN ($placeholders)"
        );
        $req->execute(array_merge([$notificationId], $allowedUserIds));
    }

    public function hideFromAllNonAdmins(int $notificationId): void
    {
        $req = $this->db->query("SELECT id_user FROM `user` WHERE is_admin = 1");
        $admins = $req->fetchAll(PDO::FETCH_COLUMN);
        $this->hideFromAllUsersExcept($notificationId, $admins);
    }

    public function markExistingNotificationsReadForUser(int $userId): void
    {
        $req = $this->db->prepare(
            "INSERT IGNORE INTO notification_read (id_user, id_notification)
             SELECT ?, id_notification FROM notification"
        );
        $req->execute([$userId]);
    }

    // Récupérer les notifications pour un utilisateur selon son niveau
    // level NULL = pour tout le monde
    public function getForUser(int $userId, string $userLevel): array
    {
        $levelMap = [
            'Débutant' => 1,
            'Perfectionnement' => 2,
            'Élémentaire' => 3,
            'Intermédiaire' => 4,
            'Confirmé' => 5,
            'Avancé' => 6,
            'Expert' => 7,
            'Élite' => 8
        ];
        $levelNum = $levelMap[$userLevel] ?? null; // null si inconnu, pas 0

        $req = $this->db->prepare("
        SELECT n.* FROM notification n
        WHERE (n.level IS NULL OR n.level = ?)
        AND n.id_notification NOT IN (
            SELECT id_notification FROM notification_read WHERE id_user = ?
        )
        ORDER BY n.created_at DESC
    ");
        $req->execute([$levelNum, $userId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marquer une notification comme lue
    public function markRead(int $userId, int $notifId): void
    {
        $req = $this->db->prepare(
            "INSERT IGNORE INTO notification_read (id_user, id_notification) VALUES (?, ?)"
        );
        $req->execute([$userId, $notifId]);
    }

    // Marquer toutes comme lues
    public function markAllRead(int $userId, string $userLevel): void
    {
        $notifications = $this->getForUser($userId, $userLevel);
        foreach ($notifications as $n) {
            $this->markRead($userId, $n['id_notification']);
        }
    }
}
