<?php

namespace app\core;

class Controller
{
    public function render($view, $params = []) {
        return Application::$app->router->renderView(str_replace(".", "/", $view), $params);
    }
}