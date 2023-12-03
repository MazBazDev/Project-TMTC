<?php

namespace app\middlewares;

use app\core\Closure;
use app\core\Middleware;
use app\core\Request;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, Closure $next)
    {
        var_dump("okokok");
        return $next->next($request);
    }
}