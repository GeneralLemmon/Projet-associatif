<?php

class TimeSlotController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lire un timeslot par ID
    public function read(int $id): ?TimeSlot
    {
        $req = $this->db->prepare("SELECT * FROM timeslot WHERE id_timeslot = ?");
        $req->execute([$id]);
        $data = $req->fetch(PDO::FETCH_ASSOC);

        return $data ? new TimeSlot($data) : null;
    }

    // Lire tous les timeslots
    public function readAll(): array
    {
        $req = $this->db->query("SELECT * FROM timeslot ORDER BY date ASC, time ASC");
        $rows = $req->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Récupérer les 2 prochains matchs d’un utilisateur
    public function getNextMatches(int $userId): array
    {
        $sql = "SELECT t.*
                FROM timeslot t
                JOIN is_registered r ON t.id_timeslot = r.id_timeslot
                WHERE r.id_user = ?
                AND CONCAT(t.date, ' ', t.time) >= NOW()
                ORDER BY t.date ASC, t.time ASC
                LIMIT 2";

        $req = $this->db->prepare($sql);
        $req->execute([$userId]);
        $rows = $req->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => new TimeSlot($row), $rows);
    }

    // Compter le nombre total de matchs joués
    public function countPlayedMatches(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM is_registered WHERE id_user = ?";
        $req = $this->db->prepare($sql);
        $req->execute([$userId]);

        return (int) $req->fetchColumn();
    }
}
