<?php

namespace app\models;

use app\core\Files;
use app\core\Models;

class Housing extends Models
{
    public $table = "housings";
    protected array $fillable = [
        "name",
        "description",
        "price",
        "created_at",
        "active"
    ];

    public function addImage(int $image_id)
    {
        $this->attach(File::class, $image_id);
    }

    public function getImages()
    {
        return $this->belongsToMany(File::class);
    }

    public function addEquipment(int $equipment_id)
    {
        $this->attach(Equipment::class, $equipment_id);
    }
    public function getEquipments()
    {
        return $this->belongsToMany(Equipment::class);
    }
}