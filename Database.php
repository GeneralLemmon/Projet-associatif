<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host = "sql207.infinityfree.com";
        $dbName = "if0_42094984_padelconnect";
        $username = "if0_42094984";
        $password = "OZ8ZUMPjIKAC";
        $port = 3306;

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbName;port=$port;charset=utf8mb4",
                $username,
                $password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
            die("<p style='color:red'>Erreur de connexion : {$error->getMessage()}</p>");
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}