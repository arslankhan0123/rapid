<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminSessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_admin) { // Check if user is admin
            $loginTime = session('login_time');
            $timeout = 30 * 60; // 30 minutes in seconds

            // If login_time is set and exceeds 30 minutes, log out the user
            if ($loginTime && (time() - $loginTime) > $timeout) {
                Auth::logout();
                session()->flush();
                return redirect('/login')->with('warning', 'You have been logged out after 30 minutes.');
            }

            // Store login time when user logs in
            if (!$loginTime) {
                session(['login_time' => time()]);
            }
        }

        return $next($request);
    }
}
