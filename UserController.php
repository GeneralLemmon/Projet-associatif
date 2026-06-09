<?php

class UserController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function read(int $id): ?User
    {
        $req = $this->db->prepare("SELECT * FROM user WHERE id_user = ?");
        $req->execute([$id]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function readAll(): array
    {
        $users = [];
        $req = $this->db->prepare("SELECT * FROM user");
        $req->execute();
        foreach ($req->fetchAll() as $data) {
            $users[] = new User($data);
        }
        return $users;
    }

    public function readByEmail(string $email): ?User
    {
        $req = $this->db->prepare("SELECT * FROM user WHERE email = ?");
        $req->execute([$email]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function readByPhone(string $phone): ?User
    {
        $req = $this->db->prepare("SELECT * FROM user WHERE phone = ?");
        $req->execute([$phone]);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function create(User $user): void
    {
        $req = $this->db->prepare(
            "INSERT INTO user (last_name, first_name, level, phone, email, password)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $req->execute([
            $user->getLastName(),
            $user->getFirstName(),
            $user->getLevel(),
            $user->getPhone(),
            $user->getEmail(),
            $user->getPassword()
        ]);
    }

    public function update(User $user): void
    {
        $req = $this->db->prepare(
            "UPDATE user 
             SET last_name=?, first_name=?, level=?, phone=?, email=?, password=?
             WHERE id_user=?"
        );

        $req->execute([
            $user->getLastName(),
            $user->getFirstName(),
            $user->getLevel(),
            $user->getPhone(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getId()
        ]);
    }

    public function delete(int $id): void
    {
        $req = $this->db->prepare("DELETE FROM user WHERE id_user = ?");
        $req->execute([$id]);
    }

    // 🔥 LOGIN EMAIL OU TÉLÉPHONE
    public function login(string $identifier, string $password): ?User
    {
        // Si c'est un email → readByEmail
        // Sinon → readByPhone
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $this->readByEmail($identifier)
            : $this->readByPhone($identifier);

        if ($user && password_verify($password, $user->getPassword())) {
            return $user;
        }
        return null;
    }
}
