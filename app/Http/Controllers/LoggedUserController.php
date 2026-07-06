<?php

namespace App\Http\Controllers;

use App\Models\LoggedUser;
use App\Queries\LoggedUserDataTable;
use Illuminate\Http\Request;
use App\Repositories\LoggedUserRepository;
use App\Services\ReverseGeocodingService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LoggedUserController extends AppBaseController
{
    private $loggedUserRepository;

    public function __construct(LoggedUserRepository $loggedUserRepo)
    {
        $this->loggedUserRepository = $loggedUserRepo;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new LoggedUserDataTable())->get($request->all()))->make(true);
        }

        $onlineUsersCount = $this->loggedUserRepository->getOnlineUsers()->count();
        $users = $this->loggedUserRepository->getUsersForSelect();
        return view('logged_users.index', compact('onlineUsersCount', 'users'));
    }

    public function getOnlineUsers()
    {
        $onlineUsers = $this->loggedUserRepository->getOnlineUsers();
        return $this->sendResponse($onlineUsers, 'Online users retrieved successfully');
    }

    public function forceLogout($id)
    {
        try {
            $loggedUser = $this->loggedUserRepository->find($id);

            if ($loggedUser) {
                // 1. Mark as force logged out
                $loggedUser->update([
                    'status' => 'force_logged_out',
                    'last_activity_at' => now(),
                    'logout_at' => now()
                ]);

                // 2. Add to forced logout table
                \DB::table('forced_logouts')->insert([
                    'user_id' => $loggedUser->user_id,
                    'reason' => 'Admin force logout',
                    'created_at' => now()
                ]);

                // 3. Destroy session
                if ($loggedUser->session_id) {
                    if (config('session.driver') === 'file') {
                        $sessionPath = config('session.files') . '/' . $loggedUser->session_id;
                        if (file_exists($sessionPath)) {
                            unlink($sessionPath);
                        }
                    }

                    if (config('session.driver') === 'database') {
                        \DB::table(config('session.table', 'sessions'))
                            ->where('id', $loggedUser->session_id)
                            ->delete();
                    }
                }
            }

            return $this->sendSuccess('User logged out successfully');
        } catch (\Exception $e) {
            \Log::error('Force logout error: ' . $e->getMessage());
            return $this->sendError('Error logging out user: ' . $e->getMessage());
        }
    }

    // public function storeBrowserLocation(Request $request)
    // {
    //     $data = $request->validate([
    //         'lat' => ['required', 'numeric'],
    //         'lng' => ['required', 'numeric'],
    //         'accuracy' => ['nullable', 'numeric'],
    //     ]);

    //     $user = Auth::user();
    //     $sessionId = $request->session()->getId();

    //     $rev = ReverseGeocodingService::reverse($data['lat'], $data['lng']);
    //     $addr = $rev['address'] ?? [];

    //     // Improved address parsing with fallbacks
    //     $state = $addr['state'] ?? $addr['region'] ?? $addr['county'] ?? null;
    //     $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['suburb'] ?? $addr['neighborhood'] ?? null;
    //     $country = $addr['country'] ?? null;
    //     $postal = $addr['postcode'] ?? $addr['postal_code'] ?? null;
    //     $formatted = $rev['display_name'] ?? null;

    //     // Merge into the same logged_users row (choose your uniqueness key)
    //     $row = LoggedUser::updateOrCreate(
    //         ['user_id' => $user->id, 'session_id' => $sessionId],
    //         [
    //             'latitude'         => $data['lat'],
    //             'longitude'        => $data['lng'],
    //             'accuracy'         => $data['accuracy'] ?? null,
    //             'region'           => $state,
    //             'city'             => $city,
    //             'country'          => $country,
    //             'postal_code'      => $postal,
    //             'address'          => $formatted,
    //             'location_source'  => 'browser',
    //             'last_activity_at' => now(),
    //             'status'           => 'online',
    //         ]
    //     );

    //     return response()->json(['ok' => true]);
    // }

    public function storeBrowserLocation(Request $request)
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();
        $sessionId = $request->session()->getId();

        $rev = ReverseGeocodingService::reverse($data['lat'], $data['lng']);
        $addr = $rev['address'] ?? [];

        // Improved address parsing with fallbacks
        $state = $addr['state'] ?? $addr['region'] ?? $addr['county'] ?? null;
        $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['suburb'] ?? $addr['neighborhood'] ?? null;
        $country = $addr['country'] ?? null;
        $postal = $addr['postcode'] ?? $addr['postal_code'] ?? null;
        $formatted = $rev['display_name'] ?? null;

        // Merge into the same logged_users row (choose your uniqueness key)
        $row = LoggedUser::updateOrCreate(
            ['user_id' => $user->id, 'session_id' => $sessionId],
            [
                'latitude'         => $data['lat'],
                'longitude'        => $data['lng'],
                'accuracy'         => $data['accuracy'] ?? null,
                'region'           => $state,
                'city'             => $city,
                'country'          => $country,
                'postal_code'      => $postal,
                'address'          => $formatted,
                'location_source'  => 'browser',
                'last_activity_at' => now(),
                'status'           => 'online',
            ]
        );

        return response()->json(['ok' => true]);
    }
}
