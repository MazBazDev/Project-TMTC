<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\models\Housing;
use app\models\HousingsType;

class HomeController extends Controller
{
    public function index()
    {
        $params = $this->request->getParams();

        if (!empty($params)) {
            $houses = $this->filter($params);
        } else {
            $houses = Housing::where(["active", true])->get();
        }

        return $this->render("home", [
            "housings" => $houses,
            "htypes" => HousingsType::all(),
        ]);
    }

    private function filter($params){

        $filters = [];

        foreach ($params as $key => $value) {
            if (strlen($value) == 0 || $value < 0) continue;

            switch ($key) {
                case "name":
                    $filters = array_merge($filters, [["name", "LIKE", $value]]);
                    break;
                case "type" :
                    if ($value == -1) break;
                    $filters = array_merge($filters, [["housing_types_id", $value]]);

                    break;
                case "city":
                    $filters = array_merge($filters, [["city", "LIKE", $value]]);
                    break;

                case "pr_min":
                    $filters = array_merge($filters, [["price", ">=", $value]]);
                    break;
                case "pr_max":
                    $filters = array_merge($filters, [["price", "<=", $value]]);
                    break;
            }

        }

        return Housing::whereArr($filters)->get();
    }
}