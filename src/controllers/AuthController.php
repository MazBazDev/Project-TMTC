<?php

namespace app\controllers;

use app\core\Application;
use app\core\Auth;
use app\core\Controller;
use app\models\User;

class AuthController extends Controller
{
    public function login()
    {
        return $this->render("auth.login");
    }

    public function login_store()
    {
        $this->request->validate([
            "email" => "bail;required;exist:app\models\User,email",
            "password" => "required",
        ], [
            "email" => [
                "required" => "email is required!!!!!",
                "exist" => "User don't exist"
            ]
        ]);

        $user = User::where(["email", $this->request->input("email")]);

        if (!password_verify($this->request->input("password"), $user->password)) {
            return $this->response
                ->redirect()
                ->back()
                ->with("inputs_errors", [
                    "password" => [
                        "Password does not match !"
                    ]
                ])
                ->with("inputs_old", $this->request->getBody());
        }

        Application::$app->auth->login($user);

        return $this->response->redirect("home")->with("success", "Logged !");
    }

    public function register()
    {
        return $this->render("auth.register");
    }

    public function register_store()
    {
        $this->request->validate([
            "firstname" => "required",
            "lastname" => "required",
            "email" => "unique:app\models\User,email;required",
            "password" => "required;min:4"
        ]);

        $user = User::create([
            "email" => strtolower($this->request->input("email")),
            "firstname" => $this->request->input("firstname"),
            "lastname" => $this->request->input("lastname"),
            "password" => password_hash($this->request->input("password"), PASSWORD_ARGON2ID),
        ]);

        setFlash("success", "registred !");
        Application::$app->auth->login($user);

        return $this->response->redirect("home")->with("success", "Logged !");
    }

    public function logout()
    {
        Application::$app->auth->logout();

        return $this->response->redirect("home")->with("success", "Lougout !");
    }

    public function profile()
    {
        return $this->render("auth.profile", [
            "user" => Auth::user(),
        ]);
    }

    public function profile_update()
    {
        $user = Auth::user();
        $this->request->validate([
            "email" => "required",
            "firstname" => "required",
            "lastname" => "required",
            "password" => "required;min:4",
            "password_confirm" => "required;equal:password"
        ]);

        if ($this->request->input("email") !== $user->email) {
            $this->request->validate([
                "email" => "unique:app\models\User,email;required",
            ]);
        }

        $user->update([
            "email" => strtolower($this->request->input("email")),
            "firstname" => $this->request->input("firstname"),
            "lastname" => $this->request->input("lastname"),
            "password" => password_hash($this->request->input("password"), PASSWORD_ARGON2ID),
        ]);

        Application::$app->auth->login($user);

        return $this->response->redirect("profile")->with("success", "Profile updated !");
    }
}
