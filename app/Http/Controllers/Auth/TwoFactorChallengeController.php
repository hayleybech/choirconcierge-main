<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\User;

class TwoFactorChallengeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
        
        if (config('two-factor.otp_throttle')) {
            $this->middleware('throttle:6,1')->only('store');
        }
    }

    /**
     * Show the two-factor challenge view.
     */
    public function create(Request $request): RedirectResponse|Response
    {
        if (!$request->session()->has('2fa.id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Attempt to authenticate a new session using the two-factor authentication code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
            'safe_device' => 'boolean',
        ]);

        $userId = $request->session()->get('2fa.id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        if ($user->validateTwoFactorCode($request->code ?? $request->recovery_code, $request->has('recovery_code'))) {
            if ($request->safe_device && config('two-factor.safe_devices.enabled')) {
                $user->addSafeDevice($request);
            }

            \Auth::login($user, $request->session()->get('2fa.remember', false));

            $request->session()->forget(['2fa.id', '2fa.remember']);

            return redirect()->to('/app/default-dash');
        }

        return back()->withErrors(['code' => 'The provided two-factor authentication code was invalid.']);
    }
}
