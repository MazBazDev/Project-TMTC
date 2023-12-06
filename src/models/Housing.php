<?php

namespace app\models;

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

}