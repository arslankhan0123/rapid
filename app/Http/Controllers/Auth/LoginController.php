<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse|mixed
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $user = Auth::user();
        // Check if OTP is enabled for the user
        if ($user->otp_enabled) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes
            $user->save();

            // Send OTP via email
            Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your OTP Code');
            });

            // Logout the user and redirect to OTP verification page
            Auth::logout();
            return redirect()->route('otp.verify')->with('email', $user->email);
        }

        if (Auth::user()->hasRole(['client'])) {
            $this->redirectTo = RouteServiceProvider::CLIENT_HOME;
        } else {
            $this->redirectTo = RouteServiceProvider::ADMIN_HOME;
        }

        if (! isset($request->remember)) {
            return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath())
                    ->withCookie(\Cookie::forget('email'))
                    ->withCookie(\Cookie::forget('password'))
                    ->withCookie(\Cookie::forget('remember'));
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath())
                ->withCookie(\Cookie::make('email', $request->email, '3600'))
                ->withCookie(\Cookie::make('password', $request->password, '3600'))
                ->withCookie(\Cookie::make('remember', '1', '3600'));
    }
}
