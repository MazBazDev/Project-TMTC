<?php

namespace app\middlewares;

use app\core\Application;
use app\core\Auth;
use app\core\Closure;
use app\core\Middleware;
use app\core\Request;

class IsAdmin implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        Application::$app->response->abort_if(!Auth::user()->isAdmin(), 403, "Unauthorized !");
        
        return $next->next($request);
    }
}