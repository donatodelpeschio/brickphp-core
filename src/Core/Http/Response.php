<?php

namespace BrickPHP\Core\Http;

class Response
{
    public function __construct(
        protected mixed $content = '',
        protected int $statusCode = 200,
        protected array $headers = []
    ) {
        $this->addHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Trasforma la risposta in JSON
     */
    public function json(array $data, int $status = 200): self
    {
        $this->content = json_encode($data);
        $this->statusCode = $status;
        $this->addHeader('Content-Type', 'application/json');
        return $this;
    }

    /**
     * Invia effettivamente la risposta al browser
     */
    public function send(): void
    {
        // Invia lo status code
        http_response_code($this->statusCode);

        // Invia gli headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Invia il corpo
        echo $this->content;
    }
}