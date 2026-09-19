<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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
    protected $redirectTo = '/app/default-dash';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(): \Inertia\Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle a login request to the application.
     *
     * @throws ValidationException
     */
    public function login(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        $remember = $request->filled('remember');

        // Manually validate credentials without logging in.
        if (\Auth::validate($credentials)) {
            $user = \Auth::getProvider()->retrieveByCredentials($credentials);

            if ($user->hasTwoFactorEnabled()) {
                // If the user has a safe device, we can bypass 2FA.
                if (config('two-factor.safe_devices.enabled') && $user->isSafeDevice($request)) {
                    \Auth::login($user, $remember);
                    return $this->sendLoginResponse($request);
                }

                // Store user ID and remember flag in session for the challenge.
                session()->put([
                    '2fa.id' => $user->getKey(),
                    '2fa.remember' => $remember,
                ]);

                return redirect()->route('auth.2fa.challenge');
            }

            // Standard login if 2FA is not enabled.
            \Auth::login($user, $remember);

            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function loggedOut(Request $request)
    {
        return redirect()->route('login');
    }
}
