<?php

namespace BrickPHP\Core\Http;

class Request
{
    public function __construct(
        protected array $get,
        protected array $post,
        protected array $server,
        protected array $files,
        protected array $cookies,
        protected ?Session $session = null
    ) {}

    /**
     * Il metodo magico per catturare la richiesta attuale
     */
    public static function capture(): self
    {
        // Supportiamo anche il parsing del body JSON per le API
        $post = $_POST;
        if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $json = json_decode(file_get_contents('php://input'), true);
            $post = array_merge($post, $json ?? []);
        }

        return new self($_GET, $post, $_SERVER, $_FILES, $_COOKIE);
    }

    public function getPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Pulizia finale: assicura che inizi con / e non finisca con / (tranne la root)
        return ($path !== '/') ? rtrim($path, '/') : $path;
    }

    public function getMethod(): string
    {
        // Supporto per il Method Spoofing (es. invio di un _method POST per simulare un DELETE)
        if ($this->post['_method'] ?? null) {
            return strtoupper($this->post['_method']);
        }

        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    public function session(): ?Session
    {
        return $this->session;
    }
}