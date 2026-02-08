<?php


namespace BrickPHP\Core;

class Logger
{
    public static function log($level, $message) {
        // Risaliamo dal vendor alla root dello Skeleton
        // Se Logger è in vendor/brickphp/core/src/Core/Logger.php
        // dobbiamo risalire di 4 livelli per arrivare a /var/www/html/
        $basePath = dirname(__DIR__, 4);
        $logDir = $basePath . '/storage/logs';

        if (!is_dir($logDir)) {
            // Usiamo @ per silenziare eventuali warning se la cartella esiste già
            // o se i permessi sono momentaneamente negati
            @mkdir($logDir, 0775, true);
        }

        $logFile = $logDir . '/app.log';
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;

        // Se non riesce a scrivere, non bloccare l'intera applicazione
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