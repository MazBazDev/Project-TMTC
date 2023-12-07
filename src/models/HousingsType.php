<?php

namespace app\models;

use app\core\Models;

class HousingsType extends Models
{
    protected $table = "housing_types";
    protected array $fillable = [
        "name",
    ];

    public function getHousings()
    {
        return Housing::where(["housing_types_id", $this->id])->get();
    }
}