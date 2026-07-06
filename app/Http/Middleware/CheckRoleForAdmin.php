<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;

class CheckRoleForAdmin
{
    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (getLoggedInUser()->hasRole('admin')) {
            return \Redirect::to(RouteServiceProvider::ADMIN_HOME);
        }

        return $next($request);
    }
}
