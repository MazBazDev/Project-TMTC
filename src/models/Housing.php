<?php

namespace app\models;

use app\core\Files;
use app\core\Models;

class Housing extends Models
{
    protected $table = "housings";
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
}