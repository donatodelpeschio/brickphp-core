<?php

namespace BrickPHP\Core;

class Logger
{
    public static function log($level, $message)
    {
        // Usiamo la costante globale definita nello Skeleton
        // Se non definita, cerchiamo di indovinare la root (fallback)
        $basePath = defined('BRICK_PATH') ? BRICK_PATH : '/var/www/html';

        // Puntiamo alla cartella storage
        $logDir = $basePath . '/storage/logs';

        // Verifichiamo se la cartella esiste, altrimenti proviamo a crearla
        if (!is_dir($logDir)) {
            // Usiamo @ per evitare crash se i permessi sono momentaneamente negati
            @mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/app.log';
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;

        // Se la cartella è scrivibile, salviamo il log
        if (is_writable($logDir) || (file_exists($logFile) && is_writable($logFile))) {
            @file_put_contents($logFile, $formattedMessage, FILE_APPEND);
        }
    }

    public static function info(string $message): void
    {
        self::log('INFO', $message);
    }

    public static function error(string $message): void
    {
        self::log('ERROR', $message);
    }
}