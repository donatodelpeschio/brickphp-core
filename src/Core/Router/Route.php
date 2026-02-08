<?php

namespace BrickPHP\Core\Router;

class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public mixed $handler,
        public array $middlewares = []
    ) {
        // Normalizziamo la URI della rotta al momento della creazione
        $this->uri = '/' . trim($uri, '/');
    }

    /**
     * Verifica se la URI richiesta corrisponde a questa rotta
     * e ne estrae i parametri.
     */
    public function matches(string $requestUri): ?array
    {
        // Normalizziamo la URI richiesta per il confronto
        $requestUri = '/' . trim($requestUri, '/');

        // Converte {id} in regex ([^/]+) per catturare i parametri dinamici
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $this->uri);
        $pattern = "#^" . $pattern . "$#i"; // Aggiunto flag 'i' per case-insensitive

        if (preg_match($pattern, $requestUri, $matches)) {
            // Filtra solo i parametri con nome (quelli catturati dalla regex)
            return array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }
}