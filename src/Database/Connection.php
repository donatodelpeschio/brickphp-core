<?php

namespace BrickPHP\Database;

use PDO;
use PDOException;
use Exception;

class Connection
{
    private static ?PDO $pdo = null;

    public static function make(array $config): PDO
    {
        if (self::$pdo === null) {
            try {
                // Supportiamo anche la porta se specificata (utile per Docker)
                $host = $config['host'] ?? '127.0.0.1';
                $port = $config['port'] ?? 3306;
                $dbname = $config['database'];

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Restituisce oggetti per default
                    PDO::ATTR_EMULATE_PREPARES => false,           // Maggiore sicurezza contro SQL Injection
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
            } catch (PDOException $e) {
                // Usiamo PDOException per catturare errori specifici del database
                throw new Exception("Errore di connessione al database: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}