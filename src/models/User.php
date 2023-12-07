<?php

namespace app\models;

use app\core\Models;

class User extends Models
{
    protected $table = "users";
    protected array $fillable = [
        "email",
        "firstname",
        "lastname",
        "profile_picture",
        "admin",
        "password",
    ];

    public function isAdmin() : bool
    {
        return $this->admin;
    }

    public function likes()
    {
        $likes = Likes::where(["users_id", $this->id])->get();

        return $likes;
    }

    public function liked($housingId)
    {
        $likes = Likes::where(["housings_id", $housingId])->get();

        if (!$likes) return false;

        foreach ($likes as $like) {
            if ($like->users_id == $this->id) {
                return true;
            }
        }

        return false;
    }

    public function getLikeds()
    {
        $liked = [];
        $likes = Likes::where(["users_id", $this->id])->get();

        foreach ($likes as $like) {
            $hous = Housing::where(["id", $like->housings_id])->first();

            if ($hous) {
                $liked[] = $hous;
            }
        }

        return $liked;
    }

    public function canComment($housingId)
    {
        $book = $this->lastBookingFor($housingId);

        if ($book === null || $book === false) return false;

        return !$book->comments_id;
    }

    public function lastBookingFor($housingId)
    {
        return Booking::whereArr([["users_id", $this->id], ["housings_id", $housingId]])->last();
    }
}