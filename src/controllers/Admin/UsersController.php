<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Auth;
use app\core\Controller;
use app\core\Request;
use app\models\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();

        return $this->render("admin.users.index", [
            "users" => $users,
        ]);
    }

    public function create()
    {
        return $this->render("admin.users.create");
    }

    public function store()
    {

        $this->request->validate([
            "firstname" => "required",
            "lastname" => "required",
            "email" => "unique:app\models\User,email;required",
            "password" => "required;min:4",
        ]);

        $user = User::create([
            "email" => strtolower($this->request->input("email")),
            "firstname" => $this->request->input("firstname"),
            "lastname" => $this->request->input("lastname"),
            "password" => password_hash($this->request->input("password"), PASSWORD_ARGON2ID),
            "admin" => $this->request->has("admin")
        ]);

        return $this->response->redirect("dashboard.users.show", ["id" => $user->id])->with("success", "User created !");
    }

    public function show($id)
    {
        $user = $this->getUserById($id);

        if ($user->id === Auth::user()->id) {
            return $this->response->redirect("profile")->with("info", "You can't edit yourself here ;)");
        }

        return $this->render("admin.users.show", [
            "user" => $user
        ]);
    }

    public function update($id)
    {
        $user = $this->getUserById($id);

        $this->request->validate([
            "firstname" => "required",
            "lastname" => "required",
            "email" => "required",
        ]);

        if ($user->email !== $this->request->input("email")) {
            $this->request->validate([
                "email" => "unique:app\models\User,email",
            ], [
                "email" => [
                    "unique" => "Email already exist for an other user !"
                ]
            ]);
        }@

        $user->update([
            "firstname" => $this->request->input("firstname"),
            "lastname" => $this->request->input("lastname"),
            "email" => $this->request->input("email"),
            "admin" => $this->request->has("admin"),
        ]);

        if ($this->request->has("password")) {
            $user->update([
                "password" => password_hash($this->request->input("password"), PASSWORD_ARGON2ID),
            ]);
        }

        return Application::$app->response->redirect("dashboard.users.show", ["id" => $id])->with("success", "User updated !");
    }

    public function delete($id) {
        $user = $this->getUserById($id);
        $user->delete();

        return Application::$app->response->redirect("dashboard.users.index")->with("success", "User deleted !");
    }

    private function getUserById($id) : User {
        $user = User::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$user);

        return $user;
    }
}