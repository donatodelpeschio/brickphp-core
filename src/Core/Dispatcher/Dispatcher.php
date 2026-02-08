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
            return $this->callAction($handler, $params, $request);
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

            return $this->callAction([$controller, $method], $params, $request);
        }

        throw new \Exception("Handler della rotta non valido", 500);
    }

    /**
     * Esegue l'azione finale assicurandosi che il ritorno sia una Response
     */
    protected function callAction(callable $callback, array $params, Request $request): Response
    {
        /**
         * Miglioramento: Ordiniamo i parametri.
         * Passiamo la Request come primo argomento, seguita dai parametri dinamici (es. {id}).
         * Questo permette ai controller di fare: index(Request $r, $id)
         */
        $arguments = array_merge([$request], array_values($params));

        // Esegue la funzione/metodo
        $result = call_user_func_array($callback, $arguments);

        // Se il controller restituisce una stringa (es. return "Ciao"), la trasformiamo in Response
        if (!$result instanceof Response) {
            return new Response((string)$result);
        }

        return $result;
    }
}