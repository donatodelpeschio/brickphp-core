<?php

namespace BrickPHP\Core;

class View
{
    public function __construct(
        protected string $path,
        protected array $data = []
    ) {}

    public function render(): string
    {
        // Usiamo BRICK_PATH per puntare alla cartella views dello Skeleton
        // Supportiamo la notazione a punti (home.index -> home/index.php)
        $basePath = defined('BRICK_PATH') ? BRICK_PATH : dirname($_SERVER['DOCUMENT_ROOT']);

        // Puoi decidere se tenerle in /app/Views o /resources/views (più moderno)
        $file = $basePath . '/app/Views/' . str_replace('.', '/', $this->path) . '.php';

        if (!file_exists($file)) {
            throw new \Exception("Vista [{$this->path}] non trovata nel percorso: $file");
        }

        // Estrae le variabili per renderle disponibili nel template
        extract($this->data);

        // Buffer di uscita per catturare l'HTML senza inviarlo subito al browser
        ob_start();

        try {
            require $file;
        } catch (\Throwable $e) {
            ob_end_clean(); // Pulisce il buffer in caso di errore nel template
            throw $e;
        }

        return ob_get_clean();
    }
}