<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function edit(): View|Response
    {
        $user = auth()->user();

        return Inertia::render('Account/Edit', [
            'two_factor_enabled' => $user->hasTwoFactorEnabled(),
            'recovery_codes' => $user->hasTwoFactorEnabled() ? $user->getRecoveryCodes() : [],
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        auth()->user()->update($request->validated());

        return redirect()
            ->route('account.edit')
            ->with(['status' => 'Account Settings updated.']);
    }
}
