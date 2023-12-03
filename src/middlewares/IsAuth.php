<?php

namespace app\middlewares;

use app\core\Application;
use app\core\Auth;
use app\core\Closure;
use app\core\Middleware;
use app\core\Request;

class IsAuth implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return Application::$app->response->redirect("/login")->with("error", "You need to be logged !");
        }
        
        return $next->next($request);
    }
}