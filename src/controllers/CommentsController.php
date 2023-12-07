<?php

namespace app\controllers;

use app\core\Application;
use app\core\Auth;
use app\core\Controller;
use app\models\Booking;
use app\models\Comments;
use app\models\Housing;
use app\models\Likes;
use DateTime;

class CommentsController extends Controller
{
    public function store($housingId)
    {
        $housing = $this->getHousingById($housingId);

        $user = Auth::user();

        if (!$user->canComment($housingId)) {
            return Application::$app->response->redirect("housings.show", ["id" => $housing->id])->with("failure", "You can't comment this housing !");
        }

        $booking = $user->lastBookingFor($housing->id);

        $comment = Comments::create([
            "bookings_id" => $booking->id,
            "housings_id" => $housing->id,
            "users_id" => $user->id,
            "stars" => $this->request->input("stars"),
            "comment" => $this->request->input("comment")
        ]);

        $booking->update([
            "comments_id" => $comment->id,
        ]);

        return Application::$app->response->redirect("housings.show", ["id" => $housing->id])->with("success", "Comment created !");
    }
    private function getHousingById($id) : Housing {
        $housing = Housing::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$housing);

        return $housing;
    }
}