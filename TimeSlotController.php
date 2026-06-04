<?php

class TimeSlotController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
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
            GROUP BY t.id_timeslot
            ORDER BY t.date ASC, t.time ASC
        ");
        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Matchs disponibles (pas encore complets, futurs, non rejoints par l'utilisateur)
    public function getAvailable(int $userId): array
    {
        $req = $this->db->prepare("
            SELECT t.*, COUNT(r.id_user) AS player_count
            FROM timeslot t
            LEFT JOIN is_registered r ON t.id_timeslot = r.id_timeslot
            WHERE t.date >= CURDATE()
            AND t.id_timeslot NOT IN (
                SELECT id_timeslot FROM is_registered WHERE id_user = ?
            )
            GROUP BY t.id_timeslot
            HAVING player_count < 4
            ORDER BY t.date ASC, t.time ASC
        ");
        $req->execute([$userId]);
        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Matchs de l'utilisateur connecté
    public function getMyMatches(int $userId): array
    {
        $req = $this->db->prepare("
            SELECT t.*, COUNT(r2.id_user) AS player_count
            FROM timeslot t
            JOIN is_registered r ON t.id_timeslot = r.id_timeslot AND r.id_user = ?
            LEFT JOIN is_registered r2 ON t.id_timeslot = r2.id_timeslot
            GROUP BY t.id_timeslot
            ORDER BY t.date ASC, t.time ASC
        ");
        $req->execute([$userId]);
        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Créer un timeslot
    public function create(string $location, string $date, string $time, int $level, int $duration = 90): void
    {
        $req = $this->db->prepare(
            "INSERT INTO timeslot (location, date, time, level, duration) VALUES (?, ?, ?, ?, ?)"
        );
        $req->execute([$location, $date, $time, $level, $duration]);
    }

    // Modifier un timeslot
    public function update(int $id, string $location, string $date, string $time, int $level, int $duration = 90): void
    {
        $req = $this->db->prepare(
            "UPDATE timeslot SET location = ?, date = ?, time = ?, level = ?, duration = ? WHERE id_timeslot = ?"
        );
        $req->execute([$location, $date, $time, $level, $duration, $id]);
    }

    // Supprimer un timeslot
    public function delete(int $id): void
    {
        $req = $this->db->prepare("DELETE FROM timeslot WHERE id_timeslot = ?");
        $req->execute([$id]);
    }

    // Rejoindre un match
    public function join(int $userId, int $timeslotId): bool
    {
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

    // Quitter un match
    public function leave(int $userId, int $timeslotId): void
    {
        $req = $this->db->prepare(
            "DELETE FROM is_registered WHERE id_user = ? AND id_timeslot = ?"
        );
        $req->execute([$userId, $timeslotId]);
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
