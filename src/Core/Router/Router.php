<?php

namespace BrickPHP\Core\Router;

class Router
{
    protected array $routes = [];

    public function get(string $uri, $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    /** * Supporto per PUT, PATCH e DELETE per architetture RESTful
     */
    public function put(string $uri, $handler): void { $this->addRoute('PUT', $uri, $handler); }
    public function patch(string $uri, $handler): void { $this->addRoute('PATCH', $uri, $handler); }
    public function delete(string $uri, $handler): void { $this->addRoute('DELETE', $uri, $handler); }

    protected function addRoute(string $method, string $uri, $handler): void
    {
        $this->routes[$method][] = new Route($method, $uri, $handler);
    }

    /**
     * Trova la rotta corrispondente alla richiesta
     */
    public function resolve(string $method, string $uri): array
    {
        $methodRoutes = $this->routes[$method] ?? [];

        foreach ($methodRoutes as $route) {
            $params = $route->matches($uri);
            if ($params !== null) {
                return [$route->handler, $params];
            }
        }

        // Se la rotta esiste ma il metodo è sbagliato, potresti lanciare un 405 Method Not Allowed
        // Per ora manteniamo il 404 per semplicità
        throw new \Exception("Pagina non trovata", 404);
    }
}