<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LoggedUser;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class TrackUserActivity
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);

    //     if (Auth::check()) {
    //         $user = Auth::user();
    //         $ip = $request->ip();

    //         // Get location data
    //         $location = Location::get($ip);

    //         // Find or create logged user record
    //         $loggedUser = LoggedUser::firstOrNew([
    //             'user_id' => $user->id,
    //             'ip_address' => $ip
    //         ]);

    //         $loggedUser->fill([
    //             'user_agent' => $request->userAgent(),
    //             'country' => $location->countryName ?? null,
    //             'city' => $location->cityName ?? null,
    //             'region' => $location->regionName ?? null,
    //             'timezone' => $location->timezone ?? null,
    //             'latitude' => $location->latitude ?? null,
    //             'longitude' => $location->longitude ?? null,
    //             'last_activity_at' => now(),
    //             'status' => 'online',
    //             'session_id' => session()->getId(),
    //         ]);

    //         if (!$loggedUser->exists) {
    //             $loggedUser->login_at = now();
    //         }

    //         $loggedUser->save();
    //     }

    //     return $response;
    // }

    // public function handle(Request $request, Closure $next)
    // {
    //     // Process the request first
    //     $response = $next($request);

    //     if (Auth::check()) {
    //         try {
    //             $user = Auth::user();
    //             $ip = $request->ip();
    //             $sessionId = $request->session()->getId();

    //             // Debug logging
    //             \Log::info('TrackUserActivity Debug', [
    //                 'user_id' => $user->id,
    //                 'ip' => $ip,
    //                 'session_id' => $sessionId,
    //                 'session_started' => $request->session()->isStarted(),
    //                 'has_session_id' => !empty($sessionId)
    //             ]);

    //             if (!$sessionId) {
    //                 \Log::warning('No session ID available for user tracking', ['user_id' => $user->id]);
    //                 return $response;
    //             }

    //             // Get location data with error handling
    //             $location = null;
    //             try {
    //                 $location = Location::get($ip);
    //                 \Log::info('Location retrieved', [
    //                     'ip' => $ip,
    //                     'country' => $location->countryName ?? 'Unknown'
    //                 ]);
    //             } catch (\Exception $e) {
    //                 \Log::error('Location service error: ' . $e->getMessage());
    //             }

    //             // Check if record already exists
    //             $existingUser = LoggedUser::where('user_id', $user->id)
    //                 ->where('session_id', $sessionId)
    //                 ->first();

    //             // Prepare data for update/create
    //             $userData = [
    //                 'ip_address'       => $ip,
    //                 'user_agent'       => $request->userAgent(),
    //                 'country'          => $location->countryName ?? null,
    //                 'city'             => $location->cityName ?? null,
    //                 'region'           => $location->regionName ?? null,
    //                 'timezone'         => $location->timezone ?? null,
    //                 'latitude'         => $location->latitude ?? null,
    //                 'longitude'        => $location->longitude ?? null,
    //                 'last_activity_at' => now(),
    //                 'status'           => 'online'
    //             ];

    //             // Set login_at only for new records
    //             if (!$existingUser) {
    //                 $userData['login_at'] = now();
    //             }

    //             // Update or create the record
    //             $loggedUser = LoggedUser::updateOrCreate(
    //                 [
    //                     'user_id' => $user->id,
    //                     'session_id' => $sessionId
    //                 ],
    //                 $userData
    //             );

    //             \Log::info('LoggedUser saved successfully', [
    //                 'user_id' => $user->id,
    //                 'logged_user_id' => $loggedUser->id,
    //                 'session_id' => $sessionId,
    //                 'was_existing' => (bool) $existingUser
    //             ]);
    //         } catch (\Exception $e) {
    //             \Log::error('TrackUserActivity middleware error: ' . $e->getMessage(), [
    //                 'user_id' => $user->id ?? 'unknown',
    //                 'trace' => $e->getTraceAsString()
    //             ]);
    //         }
    //     }

    //     return $response;
    // }

    public function handle(Request $request, Closure $next)
    {
        // Process the request first
        $response = $next($request);

        if (Auth::check()) {
            try {
                $user = Auth::user();
                $ip = $request->ip();
                $sessionId = $request->session()->getId();

                if (!$sessionId) {
                    \Log::warning('No session ID available for user tracking', ['user_id' => $user->id]);
                    return $response;
                }

                // Check if we have browser-provided location (more accurate)
                $browserLocation = LoggedUser::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->where('location_source', 'browser')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->first();

                $userData = [
                    'ip_address'       => $ip,
                    'user_agent'       => $request->userAgent(),
                    'last_activity_at' => now(),
                    'status'           => 'online'
                ];

                if ($browserLocation) {
                    // Use browser-provided location
                    $userData = array_merge($userData, [
                        'country'   => $browserLocation->country,
                        'city'      => $browserLocation->city,
                        'region'    => $browserLocation->region,
                        'latitude'  => $browserLocation->latitude,
                        'longitude' => $browserLocation->longitude,
                        'postal_code' => $browserLocation->postal_code,
                        'address'   => $browserLocation->address,
                    ]);
                } else {
                    // Fall back to IP-based location
                    try {
                        $location = Location::get($ip);
                        $userData = array_merge($userData, [
                            'country'   => $location->countryName ?? null,
                            'city'      => $location->cityName ?? null,
                            'region'    => $location->regionName ?? null,
                            'timezone'  => $location->timezone ?? null,
                            'latitude'  => $location->latitude ?? null,
                            'longitude' => $location->longitude ?? null,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Location service error: ' . $e->getMessage());
                    }
                }

                // Check if record already exists
                $existingUser = LoggedUser::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->first();

                // Set login_at only for new records
                if (!$existingUser) {
                    $userData['login_at'] = now();
                }

                // Update or create the record
                $loggedUser = LoggedUser::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'session_id' => $sessionId
                    ],
                    $userData
                );
            } catch (\Exception $e) {
                \Log::error('TrackUserActivity middleware error: ' . $e->getMessage(), [
                    'user_id' => $user->id ?? 'unknown',
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $response;
    }
}
