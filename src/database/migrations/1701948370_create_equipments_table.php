<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("equipments", function (Columns $columns) {
            $columns->id();
            $columns->string("name");
        });
    }
};
