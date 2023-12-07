<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::edit("equipments", function (Columns $columns) {
            $columns->longtext("description")->nullable(true);
        });
    }
};
