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
        ]);

        return $this->response->redirect("dashboard.equipments.show", ["id" => $equipment->id])->with("success", "Equipment created !");
    }

    public function show($id)
    {
        $equipments = $this->getEquipment($id);

        return $this->render("admin.equipments.show", [
            "equipment" => $equipments
        ]);
    }

    public function update($id)
    {
        $equipment = $this->getEquipment($id);

        $this->request->validate([
            "name" => "required",
        ]);

        $equipment->update([
            "name" => $this->request->input("name"),
        ]);

        return Application::$app->response->redirect("dashboard.equipments.show", ["id" => $id])->with("success", "Equipment updated !");
    }

    public function delete($id) {
        $equipment = $this->getEquipment($id);

//        foreach ($equipment->getImages() as $image) {
//            $equipment->detach(File::class, $image->id);
//            Files::delete($image->id);
//        }

        $equipment->delete();

        return Application::$app->response->redirect("dashboard.equipments.index")->with("success", "Equipment deleted !");
    }

    private function getEquipment($id) : Equipment {
        $equipment = Equipment::where(["id", "=", $id]) ?? false;

        Application::$app->response->abort_if(!$equipment);

        return $equipment;
    }
}