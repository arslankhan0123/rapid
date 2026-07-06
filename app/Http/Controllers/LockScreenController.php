<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    /**
     * Lock the user's screen by setting a session variable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function lock()
    {
        session(['is_locked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Screen locked successfully.',
        ]);
    }

    /**
     * Unlock the screen by validating the user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            session(['is_locked' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Screen unlocked successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid password. Please try again.',
        ], 422);
    }
}
