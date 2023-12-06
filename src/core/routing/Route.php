<?php

namespace app\core\routing;

class Route
{
    public string $method = "";

    public string $name;

    public string $path;

    public array $middlewares;

    public $callback;

    public $params;

    public function __construct(string $method, array $middlewares, string $name, string $path, $callback)
    {
        $this->method = $method;
        $this->middlewares = $middlewares;
        $this->name = $name;
        $this->path = $path;
        $this->callback = $callback;
    }

    public function matchPath($path): bool
    {
        $routePathSegments = explode('/', trim($this->path, '/'));
        $requestPathSegments = explode('/', trim($path, '/'));

        if (count($routePathSegments) !== count($requestPathSegments)) {
            return false;
        }

        $parameters = [];

        foreach ($routePathSegments as $index => $segment) {
            if (strpos($segment, ':') === 0) {
                // C'est un paramètre, stockez la valeur
                $parameterName = ltrim($segment, ':');
                $parameters[$parameterName] = $requestPathSegments[$index];
            } elseif ($segment !== $requestPathSegments[$index]) {
                // Les segments ne correspondent pas
                return false;
            }
        }
        $this->params = $parameters;

        return true;
    }
}