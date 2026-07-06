<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckForcedLogout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();

            // Check if user has been force logged out
            $forcedLogout = \DB::table('forced_logouts')
                ->where('user_id', $userId)
                ->first();

            if ($forcedLogout) {
                // Clean up the forced logout record
                \DB::table('forced_logouts')->where('user_id', $userId)->delete();

                // Force logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'You have been logged out by an administrator.',
                        'force_logout' => true
                    ], 401);
                }

                return redirect('/login')->with('error', 'You have been logged out by an administrator.');
            }
        }

        return $next($request);
    }
}
