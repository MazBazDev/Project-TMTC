<?php

namespace app\core;

class Controller
{
    public Response $response;
    public Request  $request;
    public function __construct()
    {
        $this->response = Application::$app->response;
        $this->request = Application::$app->request;
    }

    public function render($view, $params = []) {
        return Application::$app->router->renderView(str_replace(".", "/", $view), $params);
    }
}