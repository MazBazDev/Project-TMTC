<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;

class AuthController extends Controller
{
    public function login()
    {
        return $this->render("auth.login");
    }

    public function login_store()
    {
        
    }

    public function register()
    {
        return $this->render("auth.register");
    }

    public function register_store(Request $request)
    {


        var_dump($request->getBody(), $request->validate([
            "usernames" => "required"
        ]));
        return;
    }
}
