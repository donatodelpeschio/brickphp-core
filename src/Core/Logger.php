<?php


namespace BrickPHP\Core;

class Logger
{
    public static function log(string $level, string $message): void
    {
        $logPath = __DIR__ . '/../../storage/logs/app.log';
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level]: $message" . PHP_EOL;

        // Se la cartella non esiste (sicurezza extra), la creiamo
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        file_put_contents($logPath, $formattedMessage, FILE_APPEND);
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