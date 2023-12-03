<?php

namespace app\core;

class Closure
{
    private $middlewares = [];
    private $currentMiddleware = 0;

    public function addMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function next(Request $request)
    {
        if (isset($this->middlewares[$this->currentMiddleware])) {
            $middleware = $this->middlewares[$this->currentMiddleware];
            $this->currentMiddleware++;
            return $middleware->handle($request, $this);
        }

        return null;
    }
}