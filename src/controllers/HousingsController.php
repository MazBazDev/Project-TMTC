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

class HousingsController extends Controller
{
    public function show($id)
    {
        $housing = $this->getHousing($id);

        $bookings = Booking::whereArr([["housings_id", $housing->id], ["start_at", ">", time()]])->get();
        $blacklist = [];

        foreach ($bookings as $booking) {
            $startDate = new DateTime($booking->start_at);
            $endDate = new DateTime($booking->end_at);

            $blacklist[] = [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")];
        }

        return $this->render("pages.housings.show", [
            "housing" => $housing,
            "blacklist_dates" => json_encode($blacklist),
        ]);
    }

    public function like($id)
    {
        $user = Auth::user();
        if ($user->liked($id)) {
            foreach ($user->likes() as $like) {
                if ($like->housings_id == $id) {
                    $like->delete();
                    break;
                }
            }
        } else {
            Likes::create([
                "users_id" => $user->id,
                "housings_id" => $id
            ]);
        }

        return Application::$app->response->redirect("housings.show", ["id" => $id]);
    }

    public function book($hid)
    {

        $housing = $this->getHousing($hid);
        $now = date("Y/m/d");

        $this->request->validate([
            "start-date" => "required;after_date:{$now}",
            "end-date" => "required",
        ]);

        $bookings = Booking::whereArr([["housings_id", $housing->id], ["start_at", ">=", $this->request->input("start-date")], ["end_at", "<=", $this->request->input("end-date")]])->get();

        if ($bookings) {
            return Application::$app->response->redirect("housings.show", ["id" => $housing->id])->with("failure", "An booking already exist for this range !");
        }

        $booking = Booking::create([
            "users_id" => Auth::user()->id,
            "housings_id" => $housing->id,
            "start_at" => $this->request->input("start-date"),
            "end_at" => $this->request->input("end-date")
        ]);

        return Application::$app->response->redirect("housings.show", ["id" => $housing->id])->with("success", "Booking created !");
    }

    private function getHousing($id) : Housing {
        $housing = Housing::where(["id", "=", $id])->first() ?? false;

        Application::$app->response->abort_if(!$housing);

        return $housing;
    }
}