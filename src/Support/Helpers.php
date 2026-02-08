<?php

use BrickPHP\Core\Http\Request;
use BrickPHP\Core\Http\Response;
use BrickPHP\Core\Http\Session;
use BrickPHP\Database\Connection;
use BrickPHP\Database\QueryBuilder;
use BrickPHP\Cache\CacheInterface;

if (!function_exists('app')) {
    /**
     * Ritorna l'istanza globale della Request (o del Container se lo implementerai)
     */
    function request(): Request {
        return Request::capture();
    }
}

if (!function_exists('view')) {
    /**
     * Helper per renderizzare una vista
     */
    function view(string $path, array $data = []): Response {
        $viewPath = __DIR__ . '/../../app/Views/' . str_replace('.', '/', $path) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("Vista [{$path}] non trovata in {$viewPath}");
        }

        extract($data);
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        return new Response($content);
    }
}

if (!function_exists('db')) {
    /**
     * Helper per il Query Builder
     */
    function db(): QueryBuilder {
        $config = require __DIR__ . '/../../config/database.php';
        $pdo = Connection::make($config);
        return new QueryBuilder($pdo);
    }
}

if (!function_exists('cache')) {
    /**
     * Helper per la Cache (ritorna l'istanza del driver configurato)
     */
    function cache(): CacheInterface {
        $config = require __DIR__ . '/../../config/cache.php';
        $driver = $config['default'] ?? 'file';

        if ($driver === 'redis') {
            return new \BrickPHP\Cache\RedisCache($config['stores']['redis']);
        }

        $basePath = dirname(__DIR__, 2);
        return new \BrickPHP\Cache\FileCache($basePath . '/storage/cache');
    }
}

if (!function_exists('session')) {
    /**
     * Helper per gestire la sessione
     */
    function session(): Session {
        return new Session();
    }
}

if (!function_exists('config')) {
    /**
     * Helper per recuperare valori dai file di configurazione
     */
    function config(string $key, $default = null) {
        $parts = explode('.', $key);
        $file = __DIR__ . '/../../config/' . $parts[0] . '.php';

        if (!file_exists($file)) return $default;

        $config = require $file;

        // Permette l'accesso annidato tipo config('database.host')
        array_shift($parts);
        foreach ($parts as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and Die - ereditato da Symfony Var-Dumper
     */
    function dd(...$vars) {
        foreach ($vars as $v) {
            dump($v);
        }
        die(1);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) return $default;

        // Gestisce i valori booleani che nel .env sono stringhe
        switch (strtolower($value)) {
            case 'true':  return true;
            case 'false': return false;
            case 'empty': return '';
            case 'null':  return null;
        }

        return $value;
    }
}

if (!function_exists('logger')) {
    function logger() {
        return new \BrickPHP\Core\Logger();
    }
}