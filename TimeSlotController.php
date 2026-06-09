<?php

class TimeSlotController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureUserRemovedMatchTableExists();
    }

    private function ensureUserRemovedMatchTableExists(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS user_removed_match (
                id_removed_match INT(11) NOT NULL AUTO_INCREMENT,
                id_user INT(11) NOT NULL,
                id_timeslot INT(11) NOT NULL,
                removed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_removed_match),
                UNIQUE KEY uniq_user_timeslot (id_user, id_timeslot),
                KEY idx_user_id (id_user),
                KEY idx_timeslot_id (id_timeslot)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    // Lire un timeslot par ID (avec compte joueurs)
    public function read(int $id): ?TimeSlot
    {
        $req = $this->db->prepare("
            SELECT t.*, COUNT(r.id_user) AS player_count
            FROM timeslot t
            LEFT JOIN is_registered r ON t.id_timeslot = r.id_timeslot
            WHERE t.id_timeslot = ?
            GROUP BY t.id_timeslot
        ");
        $req->execute([$id]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data ? new TimeSlot($data) : null;
    }

    // Lire tous les timeslots (avec compte joueurs)
    public function readAll(): array
    {
        $req = $this->db->query("
        SELECT t.*, COUNT(r.id_user) AS player_count
        FROM timeslot t
        LEFT JOIN is_registered r ON t.id_timeslot = r.id_timeslot
        WHERE CONCAT(t.date, ' ', t.time) >= NOW()
        GROUP BY t.id_timeslot
        ORDER BY t.date ASC, t.time ASC");

        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Matchs disponibles (pas encore complets, futurs, non rejoints par l'utilisateur)
    public function getAvailable($userId): array
{
    $sql = "
        SELECT t.*, COUNT(r2.id_user) AS player_count
        FROM timeslot t
        LEFT JOIN is_registered r2 ON t.id_timeslot = r2.id_timeslot
        WHERE t.id_timeslot NOT IN (
            SELECT id_timeslot FROM is_registered WHERE id_user = ?
        )
        AND CONCAT(t.date, ' ', t.time) >= NOW()
        GROUP BY t.id_timeslot
        ORDER BY t.date ASC, t.time ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn($row) => new TimeSlot($row), $rows);
}


    public function isRemovedByAdmin(int $userId, int $timeslotId): bool
    {
        $req = $this->db->prepare(
            "SELECT COUNT(*) FROM user_removed_match WHERE id_user = ? AND id_timeslot = ?"
        );
        $req->execute([$userId, $timeslotId]);
        return (int)$req->fetchColumn() > 0;
    }

    // Matchs de l'utilisateur connecté
    public function getMyMatches($userId): array
    {
        $sql = "
        SELECT t.*, COUNT(r2.id_user) AS player_count
        FROM timeslot t
        JOIN is_registered r ON t.id_timeslot = r.id_timeslot
        LEFT JOIN is_registered r2 ON t.id_timeslot = r2.id_timeslot
        WHERE r.id_user = ?
        AND CONCAT(t.date, ' ', t.time) >= NOW()
        GROUP BY t.id_timeslot
        ORDER BY t.date ASC, t.time ASC
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new TimeSlot($row), $rows);
    }


    // Créer un timeslot
    public function create(string $location, string $date, string $time, int $level, int $duration = 90, float $price = 0.0): int
    {
        $req = $this->db->prepare(
            "INSERT INTO timeslot (location, date, time, level, duration, price) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $req->execute([$location, $date, $time, $level, $duration, $price]);
        return (int)$this->db->lastInsertId();
    }

    // Modifier un timeslot
    public function update(int $id, string $location, string $date, string $time, int $level, int $duration = 90, float $price = 0.0): void
    {
        $req = $this->db->prepare(
            "UPDATE timeslot SET location = ?, date = ?, time = ?, level = ?, duration = ?, price = ? WHERE id_timeslot = ?"
        );
        $req->execute([$location, $date, $time, $level, $duration, $price, $id]);
    }

    // Supprimer un timeslot
    public function delete(int $id): void
    {
        $req = $this->db->prepare("DELETE FROM is_registered WHERE id_timeslot = ?");
        $req->execute([$id]);

        $req = $this->db->prepare("DELETE FROM timeslot WHERE id_timeslot = ?");
        $req->execute([$id]);
    }

    // Rejoindre un match
    public function join(int $userId, int $timeslotId): bool
    {
        if ($this->isRemovedByAdmin($userId, $timeslotId)) {
            return false;
        }

        // Vérifier que le match n'est pas plein
        $req = $this->db->prepare(
            "SELECT COUNT(*) FROM is_registered WHERE id_timeslot = ?"
        );
        $req->execute([$timeslotId]);
        if ((int)$req->fetchColumn() >= 4) return false;

        // Insérer l'inscription
        $req = $this->db->prepare(
            "INSERT IGNORE INTO is_registered (id_user, id_timeslot) VALUES (?, ?)"
        );
        $req->execute([$userId, $timeslotId]);
        return true;
    }

    // Quitter un match (ou être supprimé par un admin)
    public function leave(int $userId, int $timeslotId): void
    {
        // 1. Récupérer les informations du match AVANT la suppression pour composer le message descriptif
        $slot = $this->read($timeslotId);

        // 2. Supprimer l'utilisateur du créneau
        $req = $this->db->prepare(
            "DELETE FROM is_registered WHERE id_user = ? AND id_timeslot = ?"
        );
        $req->execute([$userId, $timeslotId]);

        // 3. Logique de notification automatique (Point 7)
        // On vérifie si l'utilisateur qui déclenche l'action est un administrateur et s'il éjecte un autre joueur
        if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin']) && (int)$_SESSION['user']['id'] !== $userId) {
            $req = $this->db->prepare(
                "INSERT IGNORE INTO user_removed_match (id_user, id_timeslot) VALUES (?, ?)"
            );
            $req->execute([$userId, $timeslotId]);

            if ($slot) {
                $notifController = new NotificationController();

                // Préparation du message personnalisé avec les détails du match annulé
                $message = "L'administrateur vous a retiré du match prévu le " . $slot->getFormattedDate() . " à " . $slot->getFormattedTime() . " (" . $slot->getLocation() . ").";

                // Enregistrement de la notification
                $notifId = $notifController->create($message);

                // Masquer pour tous les comptes sauf pour le joueur exclu ($userId)
                $notifController->hideFromAllUsersExcept($notifId, [$userId]);
            }
        }
    }

    public function getPlayers(int $timeslotId): array
    {
        $req = $this->db->prepare("
        SELECT u.*
        FROM user u
        JOIN is_registered r ON u.id_user = r.id_user
        WHERE r.id_timeslot = ?
    ");
        $req->execute([$timeslotId]);

        $rows = $req->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new User($row), $rows);
    }
}
