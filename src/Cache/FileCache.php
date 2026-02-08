<?php

namespace BrickPHP\Cache;

class FileCache implements CacheInterface
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/') . '/';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->path . md5($key);
        if (!file_exists($file)) return $default;

        $data = unserialize(file_get_contents($file));
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
        return (bool) file_put_contents($this->path . md5($key), serialize($data));
    }

    public function forget(string $key): bool
    {
        $file = $this->path . md5($key);
        return file_exists($file) ? unlink($file) : true;
    }

    // Altri metodi...
    public function has(string $key): bool { return !is_null($this->get($key)); }
    public function flush(): bool { /* logic per svuotare cartella */ return true; }
}