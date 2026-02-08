<?php

namespace BrickPHP\Cache;

class RedisCache implements CacheInterface
{
    protected \Redis $redis;

    public function __construct(array $config)
    {
        $this->redis = new \Redis();
        $this->redis->connect($config['host'], $config['port']);
        if (!empty($config['password'])) {
            $this->redis->auth($config['password']);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($key);
        return ($value === false) ? $default : unserialize($value);
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->redis->setex($key, $ttl, serialize($value));
    }

    public function forget(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function has(string $key): bool { return $this->redis->exists($key); }
    public function flush(): bool { return $this->redis->flushDB(); }
}