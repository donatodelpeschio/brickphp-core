<?php

namespace BrickPHP\Core\Dispatcher;

use BrickPHP\Core\Http\Request;
use BrickPHP\Core\Http\Response;

class Dispatcher
{
    /**
     * Esegue l'handler della rotta (Controller o Closure)
     */
    public function dispatch(mixed $handler, array $params, Request $request): Response
    {
        // 1. Se l'handler è una Closure (funzione anonima)
        if ($handler instanceof \Closure) {
            return $this->callAction($handler, $params);
        }

        // 2. Se l'handler è un array [ControllerClass, 'method']
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller $controllerClass non trovato", 500);
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $method)) {
                throw new \Exception("Metodo $method non trovato in $controllerClass", 500);
            }

            // Iniettiamo la Request come primo parametro se il metodo la richiede
            return $this->callAction([$controller, $method], $params, $request);
        }

        throw new \Exception("Handler della rotta non valido", 500);
    }

    /**
     * Esegue l'azione finale assicurandosi che il ritorno sia una Response
     */
    protected function callAction(callable $callback, array $params, ?Request $request = null): Response
    {
        // Uniamo la Request ai parametri della rotta (es. id)
        $arguments = $request ? array_merge(['request' => $request], $params) : $params;

        // Esegue la funzione/metodo
        $result = call_user_func_array($callback, $arguments);

        // Se il controller restituisce una stringa, la trasformiamo in una Response
        if (!$result instanceof Response) {
            return new Response((string)$result);
        }

        return $result;
    }
}