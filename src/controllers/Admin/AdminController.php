<?php

namespace app\controllers\Admin;

use app\core\Application;
use app\core\Controller;

class AdminController extends Controller
{
    public function index() {
        return $this->render("admin.index");
    }
}