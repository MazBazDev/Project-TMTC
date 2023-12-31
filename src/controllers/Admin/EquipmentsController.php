<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Controller;
use app\models\Equipment;

class EquipmentsController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();

        return $this->render("admin.equipments.index", [
            "equipments" => $equipments,
        ]);
    }

    public function create()
    {
        return $this->render("admin.equipments.create");
    }

    public function store()
    {
        $this->request->validate([
            "name" => "required",
        ]);

        $equipment = Equipment::create([
            "name" => $this->request->input("name"),
            "description" => filter_var($this->request->input("description"), FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        return $this->response->redirect("dashboard.equipments.show", ["id" => $equipment->id])->with("success", "Equipment created !");
    }

    public function show($id)
    {
        $equipments = $this->getEquipmentById($id);
        return $this->render("admin.equipments.show", [
            "equipment" => $equipments
        ]);
    }

    public function update($id)
    {
        $equipment = $this->getEquipmentById($id);

        $this->request->validate([
            "name" => "required",
        ]);

        $equipment->update([
            "name" => $this->request->input("name"),
            "description" => filter_var($this->request->input("description"), FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        return Application::$app->response->redirect("dashboard.equipments.show", ["id" => $id])->with("success", "Equipment updated !");
    }

    public function delete($id) {
        $equipment = $this->getEquipmentById($id);

        foreach ($equipment->getHousings() as $housing) {
            $housing->detach(Equipment::class, $equipment->id);
        }

        $equipment->delete();

        return Application::$app->response->redirect("dashboard.equipments.index")->with("success", "Equipment deleted !");
    }

    private function getEquipmentById($id) : Equipment {
        $equipment = Equipment::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$equipment);

        return $equipment;
    }
}