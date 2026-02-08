<?php

use BrickPHP\Core\Http\Request;
use BrickPHP\Core\Http\Response;
use BrickPHP\Core\Http\Session;
use BrickPHP\Database\Connection;
use BrickPHP\Database\QueryBuilder;
use BrickPHP\Cache\CacheInterface;
use BrickPHP\Core\View;

// Definiamo una costante di fallback se BRICK_PATH non è presente (anche se dovrebbe esserlo)
if (!defined('BRICK_PATH')) {
    define('BRICK_PATH', '/var/www/html');
}

if (!function_exists('request')) {
    function request(): Request {
        return Request::capture();
    }
}

if (!function_exists('view')) {
    /**
     * Helper per renderizzare una vista usando la classe View del Core
     */
    function view(string $path, array $data = []): Response {
        $view = new View($path, $data);
        return new Response($view->render());
    }
}

if (!function_exists('db')) {
    /**
     * Helper per il Query Builder
     */
    function db(): QueryBuilder {
        $config = require BRICK_PATH . '/config/database.php';
        $pdo = Connection::make($config);
        return new QueryBuilder($pdo);
    }
}

if (!function_exists('cache')) {
    /**
     * Helper per la Cache
     */
    function cache(): CacheInterface {
        $config = require BRICK_PATH . '/config/cache.php';
        $driver = $config['default'] ?? 'file';

        if ($driver === 'redis') {
            return new \BrickPHP\Cache\RedisCache($config['stores']['redis']);
        }

        // Il path della FileCache ora usa BRICK_PATH
        return new \BrickPHP\Cache\FileCache(BRICK_PATH . '/storage/cache');
    }
}

if (!function_exists('session')) {
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
        $file = BRICK_PATH . '/config/' . $parts[0] . '.php';

        if (!file_exists($file)) return $default;

        $config = require $file;

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
    function dd(...$vars) {
        foreach ($vars as $v) {
            var_dump($v); // Se non hai symfony/var-dumper, usiamo var_dump
        }
        die(1);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) return $default;

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