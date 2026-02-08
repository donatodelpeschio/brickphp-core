<?php


namespace BrickPHP\Database;

use PDO;
use Exception;

class Connection
{
    private static ?PDO $pdo = null;

    public static function make(array $config): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Restituisce oggetti invece di array
                ]);
            } catch (Exception $e) {
                throw new Exception("Errore di connessione al database: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}