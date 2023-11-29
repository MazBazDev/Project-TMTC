<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("users", function (Columns $columns) {
            $columns->id();
            $columns->string("email")->unique();
            $columns->string("firstname");
            $columns->string("lastname");
            $columns->string("profile_picture")->nullable();
            $columns->boolean("admin")->defaultSQL(false)->nullable(false);
            $columns->string("password");
        });
    }
};
