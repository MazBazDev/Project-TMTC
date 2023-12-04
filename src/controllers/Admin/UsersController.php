<?php

namespace app\controllers\Admin;

use app\core\Application;
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

    public function create(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function update(Request $request, $id)
    {

        return Application::$app->response->redirect()->back()->with("success", "User updated !");
    }

    public function delete($id) {
        $user = $this->getUserById($id);

        $user->delete();

        return Application::$app->response->redirect()->back()->with("success", "User deleted !");
    }

    private function getUserById($id) : User {
        $user = User::where(["id", $id]) ?? false;

        Application::$app->response->abort_if(!$user);

        return $user;
    }
}