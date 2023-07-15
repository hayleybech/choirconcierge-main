<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImpersonateUserController extends Controller
{
    private const REDIRECT_ROUTE = 'dash';

    private const START_TOKEN_TTL = 60;   // 1 min - Should be used instantly internally

    private const STOP_TOKEN_TTL = 60 * 60; // 1 hr - Manually activated by user

    public function start(Request $request, User $user): RedirectResponse
    {
        abort_if(auth()->guest(), 405, 'Not logged in. ');

        if(! auth()->user()->membership?->hasRole('Admin') && ! auth()->user()->isSuperAdmin){
            return back()->with(['status' => 'You don\'t have permission to impersonate users', 'success' => false]);
        }

        if($user->is(auth()->user())){
            return back()->with(['status' => 'You can\'t impersonate yourself!', 'success' => false]);
        }

        if($user->isSuperAdmin) {
            return back()->with(['status' => 'Sorry, impersonation is not allowed for this user.', 'success' => false]);
        }

        if(session()->has('impersonation:active')) {
            return back()->with(['status' => 'You are already impersonating a user. Switch to your own account then try again.', 'success' => false]);
        }

        // store a token for the original user, so the user can return to normal use
        $token_stop = tenancy()->impersonate(tenancy()->tenant, auth()->id(), $this->getRedirectUrl()); // this url should be overwritten when stopping to avoid confusing the user
        session()->put('impersonation:active', true);
        session()->put('impersonation:original_user_token', $token_stop);

        // initiate impersonation
        $token_start = tenancy()->impersonate(tenancy()->tenant, $user->id, $this->getRedirectUrl());

        return UserImpersonation::makeResponse($token_start, self::START_TOKEN_TTL);
    }

    public function stop(Request $request): RedirectResponse
    {
        abort_if(auth()->guest(), 405, 'You must be logged in to do that. ');
        
        if(! session()->has('impersonation:original_user_token')){
            return back()->with(['status' => 'Sorry, could not reset impersonation. (User token not found).', 'success' => false]);
        }

        // get token and clean up session
        $token_stop = session()->pull('impersonation:original_user_token');
        session()->forget('impersonation:active');

        // abort if token too old - package does this already but we need a nicer error message here.
        if ($token_stop->created_at->diffInSeconds(Carbon::now()) > self::STOP_TOKEN_TTL) {
            $this->logout($request);

            return redirect()->route('login')->withErrors(['You have been impersonating another user for too long. The maximum is 1 hour for security reasons. Please log in again.']);
        }

        // reset token redirect url to current
        $token_stop->redirect_url = $this->getRedirectUrl();

        // end impersonation
        try {
            return UserImpersonation::makeResponse($token_stop, self::STOP_TOKEN_TTL);
        } catch (HttpException) {
            $this->logout($request);

            return redirect()->route('login')->withErrors(['An error occurred while impersonating a user. Please log in again.']);
        }
    }

    private function getRedirectUrl(): string
    {
        // Go back to previous page as new user, otherwise go to dash.
        $fallback_url = route(self::REDIRECT_ROUTE);

        return url()->previous($fallback_url);
    }

    private function logout(Request $request): void
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
