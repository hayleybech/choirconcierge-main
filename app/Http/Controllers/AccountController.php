<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use PragmaRX\Countries\Package\Countries;

class AccountController extends Controller
{
    public function edit(): View|Response
    {
        return Inertia::render('Account/Edit', [
            'countriesByRegion' => Countries::all()
                ->groupBy('region')
                ->sortBy('name'),
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        auth()->user()->update($request->validated());

        return redirect()
            ->route('singers.show', auth()->user()->membership)
            ->with(['status' => 'Account Settings updated.']);
    }
}
