<?php

namespace app\models;

use app\core\Models;

class User extends Models
{
    protected $table = "users";
    protected $fillable = [
        "email",
        "firstname",
        "lastname",
        "profile_picture",
        "admin",
        "password",
    ];
}