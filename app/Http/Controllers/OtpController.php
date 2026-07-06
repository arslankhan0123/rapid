<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function showVerifyOtp()
    {
        return view('auth.verify-otp'); // Create this view
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code != $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid OTP']);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'OTP expired, request a new one']);
        }

        // Clear OTP and log in the user
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);

        return redirect()->intended('/admin/dashboard');
    }
}
