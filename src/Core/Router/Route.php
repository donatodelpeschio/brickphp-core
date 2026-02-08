<?php

namespace BrickPHP\Core\Router;

class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public mixed $handler,
        public array $middlewares = []
    ) {}

    /**
     * Verifica se la URI richiesta corrisponde a questa rotta
     * e ne estrae i parametri.
     */
    public function matches(string $requestUri): ?array
    {
        // Converte {id} in regex ([^/]+)
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $this->uri);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $requestUri, $matches)) {
            // Filtra solo i parametri con nome
            return array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }
}