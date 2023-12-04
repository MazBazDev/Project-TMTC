<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        var_dump(Application::$app->router->associatedRoutes);
        return $this->render("home", [
            "name" => "coucou"
        ]);
    }
}