<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::edit("comments", function (Columns $columns) {
            $columns->int("housings_id");
        });
    }
};
