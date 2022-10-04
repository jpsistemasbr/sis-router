<?php

namespace SisRoute\Callback;

use Exception;
use SisRoute\Action\ClosureAction;
use SisRoute\Action\ControllerAction;
use SisRoute\Error;
use SisRoute\Http\Request;
use SisRoute\Middleware\SisRouteMiddleware;
use SisRoute\Route;

class Callback
{
    private Request $request;

    private Reflection $reflection;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->reflection = new Reflection();
    }

    public function handlerCallback(Route $route)
    {
        $this->reflection->setRequest($this->request);

        $this->runMiddlewaresIfDefined($route);
        return $this->runAction($route);
    }

    private function runMiddlewaresIfDefined(Route $route): void
    {
        if (!empty($route->getMiddlewares())) {
            $this->callMiddleware($route->getMiddlewares());
        }
    }

    private function callMiddleware(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            $obj = new $middleware();

            if (!($obj instanceof SisRouteMiddleware)) {
                $message = "Error: the class {$middleware} must implement SisRouteMiddleware.";

                Error::throwException($message);
            }

            $obj->handler($this->request);
        }
    }

    private function runAction(Route $route)
    {
        $action = $route->getAction();

        if ($action instanceof ClosureAction) {
            return $this->reflection->callFunction($action);
        }

        return $this->callObject($action);
    }

    private function callObject(ControllerAction $controllerAction)
    {
        $object = $this->reflection->callClass($controllerAction->getClass());

        return $this->reflection->callMethod($object, $controllerAction->getMethod());
    }
}