<?php

namespace app\models;

use app\core\Application;
use app\core\Models;

class File extends Models
{
    protected $table = "files";
    protected array $fillable = [
        "name",
        "path",
        "ext",
    ];
}