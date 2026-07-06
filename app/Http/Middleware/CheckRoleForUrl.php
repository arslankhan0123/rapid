<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;

class CheckRoleForUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (getLoggedInUser()->hasRole('client')) {
            return \Redirect::to(RouteServiceProvider::CLIENT_HOME);
        }

        return $next($request);
    }
}
