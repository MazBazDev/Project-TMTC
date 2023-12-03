<?php

namespace app\core;

class Controller
{
    public Response $response;
    public function __construct()
    {
        $this->response = Application::$app->response;
    }

    public function render($view, $params = []) {
        return Application::$app->router->renderView(str_replace(".", "/", $view), $params);
    }
}