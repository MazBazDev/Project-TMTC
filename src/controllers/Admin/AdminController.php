<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Controller;

class AdminController extends Controller
{
    public function index() {
        var_dump(Application::$app->router->associatedRoutes);
        return $this->render("admin.index");
    }
}