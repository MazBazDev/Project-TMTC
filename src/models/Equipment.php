<?php

namespace app\models;

use app\core\Application;
use app\core\Models;

class Equipment extends Models
{
    protected $table = "equipments";
    protected array $fillable = [
        "name",
        "description"
    ];

    public function getHousings()
    {
        return $this->belongsToManyThrough(Housing::class, 'equipments_housings', 'housings_id', 'equipments_id');
    }
}