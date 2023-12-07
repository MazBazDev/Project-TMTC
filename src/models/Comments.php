<?php

namespace app\models;

use app\core\Models;

class Comments extends Models
{
    protected $table = "comments";
    protected array $fillable = [
        "bookings_id",
        "housings_id",
        "users_id",
        "stars",
        "comment"
    ];

    public function getBooking()
    {
        return Booking::where(["id", $this->bookings_id])->first();
    }

    public function getUser()
    {
        return User::where(["id", $this->users_id])->first();
    }

    public function getHousing()
    {
        return Housing::where(["id" => $this->housings_id])->first();
    }
}