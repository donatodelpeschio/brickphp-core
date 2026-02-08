<?php


namespace BrickPHP\Cache;

interface CacheInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    public function has(string $key): bool;

    public function forget(string $key): bool;

    public function flush(): bool;
}