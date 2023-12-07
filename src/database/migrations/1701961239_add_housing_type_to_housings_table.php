<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::edit("housings", function (Columns $columns) {
            $columns->int("housing_types_id")->nullable()->defaultSQL("NULL");
        });
    }
};
