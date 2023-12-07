<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Auth;
use app\core\Controller;
use app\core\Files;
use app\models\Equipment;
use app\models\File;
use app\models\Housing;
use app\models\HousingsType;
use app\models\Services;

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
        $equipments = Equipment::all();

        return $this->render("admin.housings.create", [
            "equipments" => $equipments,
            "types" => HousingsType::all(),
            "services" => Services::all(),
        ]);
    }

    public function store()
    {
        $this->request->validate([
            "name" => "required",
            "description" => "required",
            "price" => "required;min:0",
            "type" => "required"
        ]);


        $housing = Housing::create([
            "name" => $this->request->input("name"),
            "description" => htmlspecialchars_decode($this->request->input("description")),
            "price" => $this->request->input("price"),
            "active" => $this->request->has("active"),
            "housing_types_id" => intval($this->request->input("type"))
        ]);

        $images = $this->request->getFiles("images");
        if (!empty($images)) {
            $images = Files::store($images);
            foreach ($images as $image) {
                $housing->addImage($image->id);
            }
        }

        if ($this->request->has("equipments")) {
            foreach ($this->request->input("equipments") as $equipmentId) {
                $equipment = $this->getEquipmentById($equipmentId);

                if (!$equipment) continue;

                $housing->addEquipment($equipment->id);
            }
        }

        if ($this->request->has("services")) {
            foreach ($this->request->input("services") as $serviceId) {
                $service = $this->getServiceById($serviceId);

                if (!$service) continue;

                $housing->addService($service->id);
            }
        }


        return $this->response->redirect("dashboard.housings.show", ["id" => $housing->id])->with("success", "Housing created !");
    }

    public function show($id)
    {
        $housing = $this->getHousing($id);

        $availableEquipments = array_udiff(Equipment::all(), $housing->getEquipments(), function ($obj1, $obj2) {
            return $obj1->id - $obj2->id;
        });


        $availableServices = array_udiff(Services::all(), $housing->getServices(), function ($obj1, $obj2) {
            return $obj1->id - $obj2->id;
        });

        return $this->render("admin.housings.show", [
            "housing" => $housing,
            "availableEquipments" => $availableEquipments,
            "availableServices" => $availableServices,
            "types" => HousingsType::all(),
        ]);
    }

    public function update($id)
    {
        $housing = $this->getHousing($id);

        $this->request->validate([
            "name" => "required",
            "description" => "required",
            "price" => "required;min:0",
            "type" => "required"
        ]);

        $housing->update([
            "name" => $this->request->input("name"),
            "description" => htmlspecialchars_decode($this->request->input("description")),
            "price" => $this->request->input("price"),
            "active" => $this->request->has("active"),
            "housing_types_id" => $this->request->input("type"),
        ]);

        $images = $this->request->getFiles("images");
        if (!empty($images)) {
            $images = Files::store($images);
            foreach ($images as $image) {
                $housing->addImage($image->id);
            }
        }

        if ($this->request->has("equipments")) {
            foreach ($housing->getEquipments() as $equipment) {
                $housing->detach(Equipment::class, $equipment->id);
            }

            foreach ($this->request->input("equipments") as $equipmentId) {
                $equipment = $this->getEquipmentById($equipmentId);

                if (!$equipment) continue;

                $housing->addEquipment($equipment->id);
            }
        }

        if ($this->request->has("services")) {
            foreach ($housing->getServices() as $service) {
                $housing->detach(Services::class, $service->id);
            }

            foreach ($this->request->input("services") as $serviceId) {
                $service = $this->getServiceById($serviceId);

                if (!$service) continue;

                $housing->addService($service->id);
            }
        }
        return Application::$app->response->redirect("dashboard.housings.show", ["id" => $id])->with("success", "Housing updated !");
    }

    public function delete($id) {
        $housing = $this->getHousing($id);

        foreach ($housing->getImages() as $image) {
            $housing->detach(File::class, $image->id);
            Files::delete($image->id);
        }

        foreach ($housing->getEquipments() as $eqip) {
            $housing->detach(Equipment::class, $eqip->id);
        }

        foreach ($housing->getServices() as $service) {
            $housing->detach(Services::class, $service->id);
        }

        $housing->delete();

        return Application::$app->response->redirect("dashboard.housings.index")->with("success", "Housing deleted !");
    }

    public function image_delete($id, $imageid)
    {
        $img = $this->getImageById($imageid);
        $house = $this->getHousing($id);

        if ($img === false) return;

        $house->detach(File::class, $img->id);

        Files::delete($img->id);

        return Application::$app->response->redirect("dashboard.housings.show", ["id" => $id])->with("success", "Housing updated !");

    }

    private function getImageById($id)
    {
        return File::where(["id", $id])->first() ?? false;
    }
    private function getHousing($id) : Housing {
        $housing = Housing::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$housing);

        return $housing;
    }

    private function getEquipmentById($id)
    {
        return Equipment::where(["id", $id])->first();
    }

    private function getServiceById($id)
    {
        return Services::where(["id", $id])->first();
    }
}