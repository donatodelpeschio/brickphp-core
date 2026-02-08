<?php


namespace BrickPHP\Core;

class Logger
{
    public static function log($level, $message) {
        // Risaliamo di 4 livelli per uscire da vendor/brickphp/core/src/Core/
        // e arrivare alla root dello Skeleton (/var/www/html)
        $basePath = dirname(__DIR__, 4);

        // Puntiamo alla cartella storage che abbiamo configurato nel Makefile
        $logDir = $basePath . '/storage/logs';

        if (!is_dir($logDir)) {
            // Usiamo @ per evitare che il fallimento di mkdir blocchi l'app
            // Il Makefile dovrebbe aver già creato questa cartella, ma per sicurezza:
            @mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/app.log';
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;

        @file_put_contents($logFile, $formattedMessage, FILE_APPEND);
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