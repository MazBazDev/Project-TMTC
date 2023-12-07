<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Controller;
use app\models\Services;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Services::all();

        return $this->render("admin.services.index", [
            "services" => $services,
        ]);
    }

    public function create()
    {
        return $this->render("admin.services.create");
    }

    public function store()
    {
        $this->request->validate([
            "name" => "required",
        ]);

        $service = Services::create([
            "name" => $this->request->input("name"),
            "description" => filter_var($this->request->input("description") ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        return $this->response->redirect("dashboard.services.show", ["id" => $service->id])->with("success", "Service created !");
    }

    public function show($id)
    {
        $services = $this->getServiceById($id);
        return $this->render("admin.services.show", [
            "service" => $services
        ]);
    }

    public function update($id)
    {
        $service = $this->getServiceById($id);

        $this->request->validate([
            "name" => "required",
        ]);

        $service->update([
            "name" => $this->request->input("name"),
            "description" => filter_var($this->request->input("description") ?? null, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        return Application::$app->response->redirect("dashboard.services.show", ["id" => $id])->with("success", "Service updated !");
    }

    public function delete($id) {
        $service = $this->getServiceById($id);

        foreach ($service->getHousings() as $housing) {
            $housing->detach(Services::class, $service->id);
        }

        $service->delete();

        return Application::$app->response->redirect("dashboard.services.index")->with("success", "Service deleted !");
    }


    private function getServiceById($id) : Services {
        $service = Services::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$service);

        return $service;
    }
}