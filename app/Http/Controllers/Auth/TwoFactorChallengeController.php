<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laragear\TwoFactor\Facades\Auth2FA;

class TwoFactorChallengeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        
        if (config('two-factor.otp_throttle')) {
            $this->middleware('throttle:6,1')->only('store');
        }
    }

    /**
     * Show the two factor challenge view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request): Response
    {
        if (!$request->session()->has('2fa.id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
            'safe_device' => 'boolean',
        ]);

        if ($request->recovery_code) {
            $user = Auth2FA::confirmRecovery($request->recovery_code, $request->safe_device);
        } else {
            $user = Auth2FA::confirm($request->code, $request->safe_device);
        }

        if ($user) {
            return redirect()->intended(config('auth.redirectTo', '/app/default-dash'));
        }

        return back()->withErrors(['code' => 'The provided two-factor authentication code was invalid.']);
    }
}
