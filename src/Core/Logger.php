<?php


namespace BrickPHP\Core;

class Logger
{
    public static function log($level, $message) {
        $logDir = dirname(__DIR__, 2) . '/storage/logs'; // Assicurati che il percorso sia corretto rispetto al vendor

        if (!is_dir($logDir)) {
            // Il terzo parametro 'true' permette la creazione ricorsiva
            // Usiamo @ per evitare che un fallimento blocchi l'intera app
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