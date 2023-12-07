<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("likes", function (Columns $columns) {
            $columns->id();
            $columns->int("users_id");
            $columns->int("housings_id");
        });
    }
};
