<?php

namespace app\models;

use app\core\Models;

class Services extends Models
{
    protected $table = "services";
    protected array $fillable = [
        "name",
        "description"
    ];

    public function getHousings()
    {
        return $this->belongsToMany(Housing::class);
    }
}