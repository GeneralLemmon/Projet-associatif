<?php

class UserController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    public function setDb(PDO $db): self
    {
        $this->db = $db;
        return $this;
    }

    // Récupérer un utilisateur par son ID
    public function read(int $id): ?User
    {
        $req = $this->getDb()->prepare("SELECT * FROM `user` WHERE id_user = ?");
        $req->execute([$id]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;
        return new User($data);
    }


    // Récupérer tous les utilisateurs
    public function readAll(): array
    {
        $users = [];
        $req = $this->getDb()->prepare("SELECT * FROM `user`");
        $req->execute();
        $datas = $req->fetchAll();
        foreach ($datas as $data) {
            $newUser = new User($data);
            array_push($users, $newUser);
        }
        return $users;
    }

    // Récupérer un utilisateur par son email (utile pour le login)
    public function readByEmail(string $email): ?User
    {
        $req = $this->getDb()->prepare("SELECT * FROM `user` WHERE email = ?");
        $req->execute([$email]);
        $data = $req->fetch();
        if (!$data) return null;
        return new User($data);
    }

    // Créer un utilisateur (inscription)
    public function create(User $user): void
    {
        $req = $this->getDb()->prepare(
            "INSERT INTO `user` (last_name, first_name, level, min_level, email, password) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $req->execute([
            $user->getLastName(),
            $user->getFirstName(),
            $user->getLevel(),
            $user->getMinLevel(),
            $user->getEmail(),
            $user->getPassword()
        ]);
    }

    // Modifier un utilisateur
    public function update(User $user): void
{
    $req = $this->getDb()->prepare(
        "UPDATE `user` SET last_name = ?, first_name = ?, level = ?, min_level = ?, email = ?, password = ? WHERE id_user = ?"
    );
    $req->execute([
        $user->getLastName(),
        $user->getFirstName(),
        $user->getLevel(),
        $user->getMinLevel(),
        $user->getEmail(),
        $user->getPassword(),
        $user->getId()
    ]);
}

    // Supprimer un utilisateur
    public function delete(int $id): void
    {
        $req = $this->getDb()->prepare("DELETE FROM `user` WHERE id_user = ?");
        $req->execute([$id]);
    }

    // Vérifier les identifiants (login)
    public function login(string $email, string $password): ?User
    {
        $user = $this->readByEmail($email);
        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }
        return null;
    }
}
