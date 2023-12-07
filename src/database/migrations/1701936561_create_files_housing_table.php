<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("files_housings", function (Columns $columns) {
            $columns->id();
            $columns->int("files_id");
            $columns->int("housings_id");
        });
    }
};
