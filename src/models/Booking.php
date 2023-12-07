<?php

namespace app\models;

use app\core\Application;
use app\core\Models;

class Booking extends Models
{
    protected $table = "bookings";
    protected array $fillable = [
        "users_id",
        "housings_id",
        "comments_id",
        "start_at",
        "end_at",
    ];

    public function getCommentByUser($user_id)
    {
        return Comments::whereArr([["bookings_id", $this->id], ["users_id", $user_id]])->first();
    }

    public function getHousing()
    {
        return Housing::where(["id", $this->housings_id])->first();
    }
}