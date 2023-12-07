<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("housings_services", function (Columns $columns) {
            $columns->id();
            $columns->int("housings_id");
            $columns->int("services_id");
        });
    }
};
