<?php
class NotificationController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer une notification (admin)
    public function create(string $message, ?int $level = null): void
    {
        $req = $this->db->prepare(
            "INSERT INTO notification (message, level) VALUES (?, ?)"
        );
        $req->execute([$message, $level]);
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
