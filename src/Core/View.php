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
        $file = __DIR__ . '/../../app/Views/' . str_replace('.', '/', $this->path) . '.php';

        if (!file_exists($file)) {
            throw new \Exception("Vista [{$this->path}] non trovata.");
        }

        // Estrae le variabili per renderle disponibili nel template
        extract($this->data);

        // Buffer di uscita per catturare l'HTML
        ob_start();
        require $file;
        return ob_get_clean();
    }
}