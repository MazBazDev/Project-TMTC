<?php

namespace app\core;
use app\core\twig\Helpers;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    private array $middlewares = [];
    private ?array $currentMiddleware = null;

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function middleware(array $middleware)
    {
        $this->currentMiddleware = $middleware;
        return $this;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = [$callback, $this->currentMiddleware];
        $this->currentMiddleware = null;
        return $this;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = [$callback, $this->currentMiddleware];
        $this->currentMiddleware = null;
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

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            $this->response->setStatusCode(404);
            echo $this->renderView("errors/404");
            exit;
        }

        list($handler, $middlewares) = $callback;

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

        if (is_array($handler)) {
            $handler[0] = new $handler[0]();
        }

        echo call_user_func($handler, $this->request);
        exit;
    }
}
