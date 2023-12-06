<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("housings", function (Columns $columns) {
            $columns->id();
            $columns->string("name")->nullable(false);
            $columns->longtext("description")->nullable(false);
            $columns->float("price")->nullable(false);
            $columns->timestamp("created_at")->defaultSQL("CURRENT_TIMESTAMP")->nullable(false);
        });
    }
};
