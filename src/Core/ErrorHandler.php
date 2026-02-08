<?php

namespace BrickPHP\Core;

use BrickPHP\Core\Http\Response;

class ErrorHandler
{
    public static function register(): void
    {
        // Disabilitiamo la visualizzazione degli errori nativa di PHP
        // per gestire tutto noi in modo pulito
        ini_set('display_errors', 0);
        error_reporting(E_ALL);

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $e): void
    {
        // 1. Logghiamo l'errore
        // Il Logger userà BRICK_PATH per scrivere in storage/logs
        try {
            Logger::error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        } catch (\Throwable $logError) {
            // Se fallisce pure il logger (es. permessi negati), non blocchiamo tutto
        }

        // 2. Determiniamo lo status code
        $code = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;

        // 3. Gestione Debug Mode
        // Usiamo un controllo sicuro sulla funzione env()
        $debug = function_exists('env') ? env('APP_DEBUG', true) : true;

        if ($debug) {
            $content = "<h1>Errore $code</h1>";
            $content .= "<h3>" . $e->getMessage() . "</h3>";
            $content .= "<p><strong>File:</strong> " . $e->getFile() . " (line " . $e->getLine() . ")</p>";
            $content .= "<pre>" . $e->getTraceAsString() . "</pre>";
        } else {
            $content = "<h1>Errore $code</h1><p>Si è verificato un errore interno.</p>";
        }

        // 4. Invio risposta
        // Se gli header sono già stati inviati, facciamo solo un echo
        if (headers_sent()) {
            echo $content;
        } else {
            $response = new Response($content, $code);
            $response->send();
        }

        exit;
    }

    public static function handleError($level, $message, $file, $line): void
    {
        // Converte i semplici warning/notice di PHP in ErrorException
        // Questo permette di catturarli nel blocco handleException
        if (!(error_reporting() & $level)) {
            return;
        }
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
}