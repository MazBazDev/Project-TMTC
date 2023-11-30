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

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public  function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public  function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
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

//    protected function layoutContent()
//    {
//        ob_start();
//        include_once Application::$ROOT_DIR."/views/layouts/main.php";
//        return ob_get_clean();
//    }
//
//    protected function renderOnlyView($view, $params)
//    {
//        foreach ($params as $key => $value) {
//            $$key = $value;
//        }
//
//        ob_start();
//        include_once Application::$ROOT_DIR."/views/$view.php";
//        return ob_get_clean();
//    }

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

        if (is_string($callback)) {
            echo $this->renderView($callback);
            exit;
        }

        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }

        echo call_user_func($callback, $this->request);
        exit;
    }
}
