<?php

namespace app\core;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    private ?array $currentMiddleware = null;

    private array $params = [];

    private array $routeGroups = [];

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function group(array $middleware, callable $callback)
    {
        $this->currentMiddleware = $middleware;
        $callback($this);
        $this->currentMiddleware = null;
    }

    public function middleware(array $middleware)
    {
        $this->currentMiddleware = $middleware;
        return $this;
    }

    private function addRoute($method, $path, $callback)
    {
        if (!empty($this->routeGroups)) {
            $callback = [$callback, $this->currentMiddleware];
            foreach ($this->routeGroups as $middleware) {
                $callback[1] = array_merge($callback[1], $middleware);
            }
        } else {
            $callback = [$callback, $this->currentMiddleware];
        }

        $this->routes[$method][$path] = $callback;
        $this->currentMiddleware = null;
    }

    public function get($path, $callback)
    {
        $this->addRoute('get', $path, $callback);
        return $this;
    }

    public function post($path, $callback)
    {
        $this->addRoute('post', $path, $callback);
        return $this;
    }


    public function renderView($view, $params = [])
    {
        $loader = new FilesystemLoader(Application::$ROOT_DIR . "/views");
        $twig = new Environment($loader);

        // Include the helper file
        require_once Application::$ROOT_DIR . "/core/Helpers.php";


        // Get all declared functions
        $functions = get_defined_functions();

        // Iterate through the functions and add them to Twig
        foreach ($functions['user'] as $function) {
            // Use lcfirst to make the first letter lowercase
            $twigFunctionName = lcfirst($function);

            $twig->addFunction(new \Twig\TwigFunction($twigFunctionName, $function));
        }


        return $twig->render($view . ".twig", $params);
    }

    private function matchRoute()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                // Convert :param to regular expression
                $routePattern = preg_replace_callback('/\/:([^\/]+)/', function ($matches) {
                    return '/(?<' . $matches[1] . '>[^\/]+)';
                }, $route);
                $routePattern = str_replace('/', '\/', $routePattern);
                $routePattern = '~^' . $routePattern . '$~';

                // Check if the path matches the pattern
                if (preg_match($routePattern, $path, $matches)) {

                    // Remove the first match (full path)
                    array_shift($matches);

                    // Store matched parameters
                    $matches = array_filter($matches, function ($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);

                    $this->params = $matches;

                    return $callback;
                }
            }
        }

        return false;
    }



    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $callback = $this->matchRoute();

        if ($callback === false) {
            $this->response->setStatusCode(404);
            echo $this->renderView("errors/404");
            exit;
        }

        list($handler, $middlewares) = $callback;

        if (!empty($this->routeGroups)) {
            foreach ($this->routeGroups as $groupMiddleware) {
                $middlewares = array_merge($middlewares, $groupMiddleware);
            }
        }

        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {
                $middlewareInstance = new $middleware();
                $closure = new Closure();
                $closure->addMiddleware($middlewareInstance);
                $closure->next($this->request);
            }
        }

        if (is_string($handler)) {
            echo $this->renderView($handler);
            exit;
        }

        if (is_array($callback)) {
            $handler = $callback[0][0];
            $method = $callback[0][1];

            if (is_string($handler)) {
                $handler = new $handler();
            }

            echo call_user_func_array([$handler, $method],  $this->params);

            exit;
        }

        echo call_user_func($handler, $this->request);
        exit;
    }
}
