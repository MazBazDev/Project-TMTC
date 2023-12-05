<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("files", function (Columns $columns) {
            $columns->id();
            $columns->longtext("name");
            $columns->longtext("path");
            $columns->string("ext");
        });
    }
};
