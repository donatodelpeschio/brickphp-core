<?php

namespace BrickPHP\Core\Http;

class Session
{
    public function __construct()
    {
        // Usiamo la costante definita nello Skeleton per trovare la cartella storage
        // Aggiungiamo un fallback per sicurezza nel caso la costante non sia definita
        $base = defined('BRICK_PATH') ? BRICK_PATH : dirname($_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html');
        $savePath = $base . '/storage/sessions';

        // Assicuriamoci che la cartella esista
        if (!is_dir($savePath)) {
            @mkdir($savePath, 0775, true);
        }

        // Diciamo a PHP di usare questa cartella per le sessioni
        // Questo evita che PHP usi la cartella di sistema (spesso non scrivibile in Docker)
        if (is_writable($savePath)) {
            session_save_path($savePath);
        }

        if (session_status() === PHP_SESSION_NONE) {
            // Impediamo l'invio di cookie di sessione se gli header sono già stati inviati
            if (!headers_sent()) {
                session_start();
            }
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Gestione messaggi Flash (usa e getta)
     */
    public function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = [];
        }
    }
}