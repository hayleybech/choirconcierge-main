<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class TwoFactorController extends Controller
{
    /**
     * Show the Two-Factor Authentication settings.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        $enabled = $user->hasTwoFactorEnabled();
        
        $data = [
            'enabled' => $enabled,
        ];

        if (!$enabled) {
            // Generate secret but don't save yet (or createTwoFactorAuth does it)
            // README says createTwoFactorAuth returns a SharedSecret.
            // If we want to show QR code before enabling, we call createTwoFactorAuth.
            $secret = $user->createTwoFactorAuth();
            $data['qr_code'] = $secret->toQr();
            $data['uri'] = $secret->toUri();
            $data['string'] = $secret->toString();
        } else {
            $data['recovery_codes'] = $user->getRecoveryCodes();
        }

        return Inertia::render('Account/TwoFactor', $data);
    }

    /**
     * Enable Two-Factor Authentication.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $activated = $request->user()->confirmTwoFactorAuth($request->code);

        if ($activated) {
            return back()->with(['status' => 'Two-Factor Authentication has been enabled.']);
        }

        return back()->withErrors(['code' => 'The provided code was invalid.']);
    }

    /**
     * Disable Two-Factor Authentication.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->disableTwoFactorAuth();

        return back()->with(['status' => 'Two-Factor Authentication has been disabled.']);
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerate(Request $request): RedirectResponse
    {
        $request->user()->generateRecoveryCodes();

        return back()->with(['status' => 'Recovery codes have been regenerated.']);
    }
}
