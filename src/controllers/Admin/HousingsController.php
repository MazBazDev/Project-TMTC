<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Auth;
use app\core\Controller;
use app\models\Housing;

class HousingsController extends Controller
{
    public function index()
    {
        $housings = Housing::all();

        return $this->render("admin.housings.index", [
            "housings" => $housings,
        ]);
    }

    public function create()
    {
        return $this->render("admin.housings.create");
    }

    public function store()
    {
        $this->request->validate([
            "name" => "required",
            "description" => "required",
            "price" => "required;min:0",
        ]);

        $housing = Housing::create([
            "name" => $this->request->input("name"),
            "description" => htmlspecialchars_decode($this->request->input("description")),
            "price" => $this->request->input("price"),
            "active" => $this->request->has("active")
        ]);

        return $this->response->redirect("dashboard.housings.show", ["id" => $housing->id])->with("success", "Housing created !");
    }

    public function show($id)
    {
        $housing = $this->getHousing($id);

        return $this->render("admin.housings.show", [
            "housing" => $housing
        ]);
    }

    public function update($id)
    {
        $housing = $this->getHousing($id);

        $this->request->validate([
            "name" => "required",
            "description" => "required",
            "price" => "required;min:0",
        ]);


        $housing->update([
            "name" => $this->request->input("name"),
            "description" => htmlspecialchars_decode($this->request->input("description")),
            "price" => $this->request->input("price"),
            "active" => $this->request->has("active")
        ]);

        return Application::$app->response->redirect()->back()->with("success", "Housing updated !");
    }

    public function delete($id) {
        $housing = $this->getHousing($id);
        $housing->delete();

        return Application::$app->response->redirect("dashboard.housings.index")->with("success", "Housing deleted !");
    }

    private function getHousing($id) : Housing {
        $housing = Housing::where(["id", "=", $id]) ?? false;

        Application::$app->response->abort_if(!$housing);

        return $housing;
    }
}