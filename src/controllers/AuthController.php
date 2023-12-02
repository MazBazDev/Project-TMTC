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
            "firstname" => "required",
            "lastname" => "required",
            "email" => "unique:app\models\User,email;required",
            "password" => "required;min:4"
        ]);


        $user = User::create([
            "email" => strtolower($request->input("email")),
            "firstname" => $request->input("firstname"),
            "lastname" => $request->input("lastname"),
            "password" => password_hash($request->input("password"), PASSWORD_ARGON2ID),
        ]);
        var_dump($user);
        die();
        return;
    }
}
