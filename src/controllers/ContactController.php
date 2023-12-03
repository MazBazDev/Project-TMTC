<?php

namespace app\controllers;

use app\core\Application;
use app\core\Request;

class ContactController
{
    public static function index($id, $te)
    {
        var_dump($id);

        return Application::$app->router->renderView("contact");
    }

    public static function handleContact(Request $request, $id)
    {
        return "handling submited datas !";
    }
}