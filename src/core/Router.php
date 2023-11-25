<?php

namespace app\core;
use app\controllers\ContactController;

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
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        ob_start();
        include_once Application::$ROOT_DIR."/views/layouts/main.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once Application::$ROOT_DIR."/views/$view.php";
        return ob_get_clean();
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
