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

    public function getPath()
    {
        return env("APP_URL") . $this->path . $this->ext;
    }
}