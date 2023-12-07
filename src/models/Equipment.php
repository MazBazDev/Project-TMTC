<?php

namespace app\models;

use app\core\Models;

class Equipment extends Models
{
    protected $table = "equipments";
    protected array $fillable = [
        "name",
    ];
}