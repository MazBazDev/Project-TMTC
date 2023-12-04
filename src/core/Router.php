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
    private string $prefix = "";
    private string $assetDirectory;
    private ?string $currentName = null;
    public array $associatedRoutes = [];
    private array $currentRouteGroups = [];

    private array $parentMiddlewares = [];

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->assetDirectory = config("assetDir");
    }

    public function group(array $middleware, callable $callback)
    {
        $parentGroup = $this->currentRouteGroups;

        // Inclure les middlewares des groupes parents
        $middleware = array_merge($middleware, $this->parentMiddlewares);

        if ($this->currentName !== null) {
            $this->currentRouteGroups[] = $this->currentName;
        }

        $this->currentMiddleware = $middleware;

        // Inclure les middlewares des groupes parents pour les groupes imbriqués
        $this->parentMiddlewares = array_merge($this->parentMiddlewares, $this->currentMiddleware);

        $callback($this);

        $this->currentMiddleware = null;
        $this->currentName = null;
        $this->currentRouteGroups = $parentGroup;

        // Restaurer les middlewares des groupes parents après la sortie du groupe
        $this->parentMiddlewares = array_slice($this->parentMiddlewares, 0, -count($middleware));

        return $this;
    }


    public function middleware(array $middleware)
    {
        $this->currentMiddleware = $middleware;
        return $this;
    }

    public function prefix(string $prefix)
    {
        $this->prefix .= $prefix;
        return $this;
    }

    public function name(string $name)
    {
        $fullName = $name;
        if (!empty($this->currentRouteGroups)) {
            $fullName = end($this->currentRouteGroups) . '.' . $name;
        }

        if ($this->currentName !== null) {
            $this->currentName .= '.' . $name;
        } else {
            $this->currentName = $fullName;
        }

        return $this;
    }

    private function addRoute($method, $path, $callback)
    {
        if ($this->prefix != "") {
            $path = $this->prefix . $path;
        }

        if ($this->currentName !== null) {
            $this->associatedRoutes[$this->currentName] = ($path !== '/' ? rtrim($path, '/') : $path);
        }

        // Inclure les middlewares des groupes parents pour la route
        $middlewares = array_merge($this->currentMiddleware ?? [], $this->parentMiddlewares);

        if (!empty($this->routeGroups)) {
            $callback = [$callback, $middlewares];
            foreach ($this->routeGroups as $middleware) {
                $callback[1] = array_merge($callback[1], $middleware);
            }
        } else {
            $callback = [$callback, $middlewares];
        }

        $this->routes[$method][$path] = $callback;
        $this->currentMiddleware = null;
        $this->currentName = null;
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

    public function delete($path, $callback)
    {
        $this->addRoute('delete', $path, $callback);
        return $this;
    }

    public function patch($path, $callback)
    {
        $this->addRoute('patch', $path, $callback);
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

        echo $twig->render($view . ".twig", $params);
    }

    private function matchRoute()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        // Check if it's an asset route
        if ($method === 'asset') {
            $assetPath = $this->assetDirectory . '/' . ltrim($path, '/');
            if (file_exists($assetPath) && is_file($assetPath)) {
                // Serve the asset directly
                $this->response->abort_if(!$this->response->serveAsset($assetPath));
                exit;
            }
        }

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => $callback) {
                // Convert :param to regular expression
                $routePattern = preg_replace_callback('/\/:([^\/]+)/', function ($matches) {
                    return '/(?<' . $matches[1] . '>[^\/]+)';
                }, $route);

                // Allow zero or one occurrence of / at the end of the route
                $routePattern = '~^' . rtrim($routePattern, '/') . '\/?$~';

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
        $callback = $this->matchRoute();
        $this->response->abort_if($callback === false);
        list($handler, $middlewares) = $callback;

        if (!empty($this->routeGroups)) {
            foreach ($this->routeGroups as $groupMiddleware) {
                $this->parentMiddlewares = array_merge($this->parentMiddlewares, $groupMiddleware);
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
            $this->renderView($handler);
        }

        if (is_array($callback)) {
            $handler = $callback[0][0];
            $method = $callback[0][1];

            if (is_string($handler)) {
                $handler = new $handler();
            }


            echo call_user_func_array([$handler, $method], $this->params);
            exit;
        }

        echo call_user_func($handler, $this->request);
        exit;
    }
}
