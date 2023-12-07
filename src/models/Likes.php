<?php

namespace app\models;

use app\core\Application;
use app\core\Models;

class Likes extends Models
{
    protected $table = "likes";
    protected array $fillable = [
        "users_id",
        "housings_id",
    ];
}