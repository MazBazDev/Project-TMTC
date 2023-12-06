<?php

namespace app\core\routing;

use app\core\Application;
use app\core\Closure;
use app\core\Request;
use app\core\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class Router
{
    private Request $request;
    private Response $response;

    public array $routes = [];
    private array $currentName = [];
    private array $currentMiddlewares = [];

    private array $currentPath = [];

    private bool $in_array = false;

    private int $middlewaresCount = 0;

    public array $associatedRoutes = [];


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($callback)
    {
        $this->addRoute("get", $callback);
    }

    public function post($callback)
    {
        $this->addRoute("post", $callback);
    }

    public function delete($callback)
    {
        $this->addRoute("delete", $callback);
    }

    public function patch($callback)
    {
        $this->addRoute("patch", $callback);
    }

    public function path($path)
    {
        $this->currentPath[] = $path;
        return $this;
    }

    public function name(string $name): Router
    {
        $this->currentName[] = $name;

        return $this;
    }

    public function middlewares(array $middlewares = []): Router
    {
        $this->middlewaresCount = sizeof($middlewares);
        $this->currentMiddlewares = array_merge($this->currentMiddlewares, $middlewares);
        return $this;
    }


    public function addRoute($method, $callback)
    {
        $name = implode(".", $this->currentName);
        $path = implode("/", $this->currentPath);
        $path = str_replace("//", "/", $path);

        if ($path !== "/") {
            $path = rtrim($path, "/");
        }

        $this->routes[$method][] = new Route($method, $this->currentMiddlewares, $name, $path, $callback);
        $this->associatedRoutes[$name] = $path;

        if (!$this->in_array) {
            $this->currentPath = [];
            $this->currentMiddlewares = [];
            $this->currentName = [];
        } else {
            array_pop($this->currentPath);
            array_splice($this->currentMiddlewares, $this->middlewaresCount * -1);
            array_pop($this->currentName);
        }
    }

    public function group(callable $callback): Router
    {
        $this->in_array = true;

        $callback($this);


        $this->in_array = false;
        array_pop($this->currentPath);
        $this->currentMiddlewares = [];
        array_pop($this->currentName);
        return $this;
    }

    public function matchRouteToRequest()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();


        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route) {
                if ($route->matchPath($path)) {
                    return $route;
                }
            }
        }

        return false;
    }

    public function resolve()
    {
        $route = $this->matchRouteToRequest();

        $this->response->abort_if($route === false);

        if (!empty($route->middlewares)) {
            foreach ($route->middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $closure = new Closure();
                $closure->addMiddleware($middlewareInstance);
                $closure->next($this->request);
            }
        }


        if (is_string($route->callback)) {
            $this->renderView($route->callback);
            exit;
        }

        if (is_array($route->callback)) {
            [$handler, $method] = $route->callback;

            if (is_string($handler)) {
                $handler = new $handler();
            }

            echo call_user_func_array([$handler, $method], $route->params);
            exit;
        }

        echo call_user_func($route->callback, $this->request);
        exit;
    }

    public function renderView(string $view, array $params = [])
    {
        $loader = new FilesystemLoader(Application::$ROOT_DIR . "/views");
        $twig = new Environment($loader);

        //Load helpers function on twig env

        require_once Application::$ROOT_DIR . "/core/Helpers.php";

        $functions = get_defined_functions();

        foreach ($functions['user'] as $function) {
            $twigFunctionName = lcfirst($function);
            $twig->addFunction(new \Twig\TwigFunction($twigFunctionName, $function));
        }

        echo $twig->render($view . ".twig", $params);
    }
}