<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Controller;
use app\models\Equipment;
use app\models\HousingsType;

class HousingsTypesController extends Controller
{
    public function index()
    {
        $types = HousingsType::all();

        return $this->render("admin.types.index", [
            "types" => $types,
        ]);
    }

    public function create()
    {
        return $this->render("admin.types.create");
    }

    public function store()
    {
        $this->request->validate([
            "name" => "required",
        ]);

        $equipment = HousingsType::create([
            "name" => $this->request->input("name"),
        ]);

        return $this->response->redirect("dashboard.housings.types.show", ["id" => $equipment->id])->with("success", "Type created !");
    }

    public function show($id)
    {
        $types = $this->getType($id);
        return $this->render("admin.types.show", [
            "type" => $types
        ]);
    }

    public function update($id)
    {
        $type = $this->getType($id);

        $this->request->validate([
            "name" => "required",
        ]);

        $type->update([
            "name" => $this->request->input("name"),
        ]);

        return Application::$app->response->redirect("dashboard.housings.types.show", ["id" => $id])->with("success", "Type updated !");
    }

    public function delete($id) {

        $type = $this->getType($id);

        $housing = $type->getHousings();
        if (!empty($housing)) {
            foreach ($housing as $housing) {
                $housing->update([
                    "housing_types_id" => null,
                ]);
            }
        }

        $type->delete();

        return Application::$app->response->redirect("dashboard.housings.types.index")->with("success", "Type deleted !");
    }

    private function getType($id) : HousingsType {
        $equipment = HousingsType::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$equipment);

        return $equipment;
    }
}