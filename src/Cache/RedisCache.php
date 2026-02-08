<?php

namespace BrickPHP\Cache;

class RedisCache implements CacheInterface
{
    protected \Redis $redis;

    public function __construct(array $config)
    {
        $this->redis = new \Redis();

        // Usiamo un blocco try-catch o verifichiamo la connessione per evitare crash bloccanti
        try {
            $host = $config['host'] ?? '127.0.0.1';
            $port = $config['port'] ?? 6379;

            $this->redis->connect($host, (int) $port);

            if (!empty($config['password'])) {
                $this->redis->auth($config['password']);
            }
        } catch (\Exception $e) {
            // Se Redis è giù, in produzione potresti voler loggare l'errore
            // invece di interrompere l'esecuzione.
            throw new \Exception("Impossibile connettersi a Redis: " . $e->getMessage());
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($key);

        if ($value === false) {
            return $default;
        }

        // Unserialize può fallire se il dato è salvato male, meglio gestire l'errore
        $decoded = @unserialize($value);
        return ($decoded === false && $value !== serialize(false)) ? $value : $decoded;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        // Se il valore è già una stringa o un numero, potremmo salvarlo così com'è,
        // ma la serializzazione garantisce consistenza per oggetti e array.
        $serialized = serialize($value);
        return $this->redis->setex($key, $ttl, $serialized);
    }

    public function forget(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    public function flush(): bool
    {
        return $this->redis->flushDB();
    }
}