<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
            'statesForSelectedCountry' => auth()
                ->user()
                ?->address_country
                ?->hydrateStates()
                ->states
                ->filter(fn ($state) => !Str::of($state->iso_3166_2)->contains('~')) // Only support territories with a valid ISO 3166-2 code
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
