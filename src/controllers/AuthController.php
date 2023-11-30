<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\models\User;

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
        $request->validate([
            "usernames" => "required",
            "email" => "unique:app\models\User,email"
        ]);

//        var_dump($request->getBody());
        return;
    }
}
