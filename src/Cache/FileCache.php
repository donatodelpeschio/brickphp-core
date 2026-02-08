<?php

namespace BrickPHP\Cache;

class FileCache implements CacheInterface
{
    protected string $path;

    public function __construct(?string $path = null)
    {
        // Se non viene passato un percorso, usiamo quello di default basato su BRICK_PATH
        // Altrimenti usiamo quello ricevuto (utile per i test o configurazioni custom)
        $targetPath = $path ?: BRICK_PATH . '/storage/cache';

        $this->path = rtrim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Assicuriamoci che la cartella esista e sia scrivibile
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0775, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->path . md5($key);
        if (!file_exists($file)) {
            return $default;
        }

        $content = @file_get_contents($file);
        if (!$content) {
            return $default;
        }

        $data = unserialize($content);

        if (time() > $data['expires']) {
            $this->forget($key);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $data = [
            'expires' => time() + $ttl,
            'value' => $value
        ];

        $file = $this->path . md5($key);
        return (bool) @file_put_contents($file, serialize($data));
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string $key): bool
    {
        $file = $this->path . md5($key);
        return file_exists($file) ? @unlink($file) : true;
    }

    public function flush(): bool
    {
        // Logica per svuotare la cartella cache
        foreach (glob($this->path . '*') as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        return true;
    }
}