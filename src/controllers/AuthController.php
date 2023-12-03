<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return $this->render("auth.login");
    }

    public function login_store(Request $request)
    {

        $request->validate([
            "email" => "bail;required;exist:app\models\User,email",
            "password" => "required",
        ], [
            "email" => [
                "required" => "email is required!!!!!",
                "exist" => "User don't exist"
            ]
        ]);

        $user = User::where(["email", $request->input("email")]);

        if (!password_verify($request->input("password"), $user->password)) {
            return $this->response
                ->redirect()
                ->back()
                ->with("inputs_errors", [
                    "password" => [
                        "Password does not match !"
                    ]
                ])
                ->with("inputs_old", $request->getBody());
        }

        Application::$app->auth->login($user);

        return $this->response->redirect("/")->with("success", "Logged !");
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

        setFlash("success", "registred !");
        Application::$app->auth->login($user);

        return $this->response->redirect("/")->with("success", "Logged !");
    }

    public function logout()
    {
        Application::$app->auth->logout();

        return $this->response->redirect("/")->with("success", "Lougout !");
    }
}
