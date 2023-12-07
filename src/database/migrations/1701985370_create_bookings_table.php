<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("bookings", function (Columns $columns) {
            $columns->id();
            $columns->int("users_id");
            $columns->int("housings_id");
            $columns->int("comments_id")->nullable()->defaultSQL("NULL");
            $columns->timestamp("start_at")->nullable();
            $columns->timestamp("end_at")->nullable();
        });
    }
};
