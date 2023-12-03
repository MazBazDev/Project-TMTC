<?php

namespace app\middlewares;

use app\core\Application;
use app\core\Auth;
use app\core\Closure;
use app\core\Middleware;
use app\core\Request;

class IsNotAuth implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return Application::$app->response->redirect("/");
        }
        
        return $next->next($request);
    }
}