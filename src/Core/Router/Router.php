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

        throw new \Exception("Route not found", 404);
    }
}