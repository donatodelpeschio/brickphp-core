<?php


namespace BrickPHP\Core;

use BrickPHP\Core\Http\Response;

class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $e): void
    {
        // 1. Logghiamo l'errore nel file
        Logger::error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

        // 2. Risposta all'utente
        $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;

        // Se siamo in dev (potresti usare env('APP_DEBUG')), mostriamo i dettagli, altrimenti un messaggio generico
        $message = env('APP_DEBUG', true) ? $e->getMessage() : "Si è verificato un errore interno.";

        $response = new Response("<h1>Errore $code</h1><p>$message</p>", $code);
        $response->send();
        exit;
    }

    public static function handleError($level, $message, $file, $line): void
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
}