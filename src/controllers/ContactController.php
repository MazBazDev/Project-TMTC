<?php

namespace app\controllers;

use app\core\Application;
use app\core\Request;

class ContactController
{
    public static function index()
    {
        return Application::$app->router->renderView("contact");
    }

    public static function handleContact(Request $request)
    {
        var_dump($request->getBody());
        return "handling submited datas !";
    }
}